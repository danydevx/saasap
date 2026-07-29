<template>
  <div v-if="isAvailable" class="ai-chat-widget" :class="[widgetTheme, { open: isOpen }]">
    <div v-if="!isOpen" class="chat-bubble" :style="{ backgroundColor: widgetColor }" @click="openChat">
      <div class="bubble-icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
          <path d="M12 2C6.48 2 2 5.58 2 10c0 1.82.62 3.49 1.64 4.83L2 22l4.17-.64A9.93 9.93 0 0012 22c5.52 0 10-3.58 10-8s-4.48-12-10-12z" fill="white"/>
          <circle cx="8" cy="10" r="1.5" :fill="widgetColor"/>
          <circle cx="12" cy="10" r="1.5" :fill="widgetColor"/>
          <circle cx="16" cy="10" r="1.5" :fill="widgetColor"/>
        </svg>
      </div>
      <div class="bubble-badge" v-if="unreadCount > 0">{{ unreadCount }}</div>
    </div>

    <div v-else class="chat-window">
      <div class="chat-header" :style="{ backgroundColor: widgetColor }">
        <div class="chat-header-info">
          <div class="chat-avatar">
            <i class="bi bi-robot"></i>
          </div>
          <div>
            <div class="chat-title">Asistente {{ businessName }}</div>
            <div class="chat-status">
              <span class="status-dot"></span> En línea
            </div>
          </div>
        </div>
        <div class="chat-header-actions">
          <button v-if="allowReset" class="chat-action-btn" @click="resetChat" title="Reiniciar chat">
            <i class="bi bi-arrow-counterclockwise"></i>
          </button>
          <button class="chat-close-btn" @click="closeChat">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>

      <div class="chat-messages" ref="messagesContainer">
        <div v-if="messages.length === 0" class="chat-empty">
          <i class="bi bi-chat-dots"></i>
          <p>¡Hola! Soy el asistente virtual de {{ businessName }}. ¿En qué puedo ayudarte?</p>
        </div>

        <div
          v-for="(msg, index) in messages"
          :key="index"
          class="chat-message"
          :class="msg.role"
        >
          <div class="message-content">
            {{ msg.content }}
          </div>
          <div class="message-time" v-if="msg.timestamp">
            {{ formatTime(msg.timestamp) }}
          </div>
        </div>

        <div v-if="isTyping" class="chat-message assistant">
          <div class="message-content typing">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>
      </div>

      <div class="chat-input">
        <input
          type="text"
          v-model="inputMessage"
          placeholder="Escribe un mensaje..."
          :disabled="sending"
          @keypress.enter="sendMessage"
        />
        <button
          class="send-btn"
          :style="{ backgroundColor: widgetColor }"
          :disabled="!inputMessage.trim() || sending"
          @click="sendMessage"
        >
          <i v-if="sending" class="bi bi-hourglass-split"></i>
          <i v-else class="bi bi-send"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, watch } from 'vue'

const props = defineProps({
  businessSlug: {
    type: String,
    required: true,
  },
  businessName: {
    type: String,
    default: '',
  },
  widgetColor: {
    type: String,
    default: '#3B82F6',
  },
  widgetTheme: {
    type: String,
    default: 'light',
  },
  allowReset: {
    type: Boolean,
    default: false,
  },
})

const isOpen = ref(false)
const isAvailable = ref(true)
const messages = ref([])
const inputMessage = ref('')
const sending = ref(false)
const isTyping = ref(false)
const messagesContainer = ref(null)
const unreadCount = ref(0)

const sessionId = ref(localStorage.getItem(`chat_session_${props.businessSlug}`) || generateSessionId())

function generateSessionId() {
  const id = 'chat_' + Math.random().toString(36).substring(2) + Date.now().toString(36)
  localStorage.setItem(`chat_session_${props.businessSlug}`, id)
  return id
}

const openChat = () => {
  isOpen.value = true
  unreadCount.value = 0
  loadHistory()
  nextTick(() => scrollToBottom())
}

const closeChat = () => {
  isOpen.value = false
}

const resetChat = () => {
  if (confirm('¿Quieres reiniciar la conversación? Se borrarán todos los mensajes.')) {
    messages.value = []
    sessionStorage.removeItem(`chat_messages_${props.businessSlug}_${sessionId.value}`)
    sessionId.value = generateSessionId()
  }
}

const loadHistory = () => {
  const saved = sessionStorage.getItem(`chat_messages_${props.businessSlug}_${sessionId.value}`)
  if (saved) {
    try {
      messages.value = JSON.parse(saved)
    } catch (e) {
      messages.value = []
    }
  }
}

const saveMessages = () => {
  sessionStorage.setItem(
    `chat_messages_${props.businessSlug}_${sessionId.value}`,
    JSON.stringify(messages.value)
  )
}

const sendMessage = () => {
  if (!inputMessage.value.trim() || sending.value) return

  const userMessage = inputMessage.value.trim()
  messages.value.push({
    role: 'user',
    content: userMessage,
    timestamp: new Date().toISOString(),
  })
  inputMessage.value = ''
  saveMessages()
  scrollToBottom()

  sending.value = true
  isTyping.value = true

  fetch(`/m/${props.businessSlug}/ai-chatbot/chat`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
    },
    body: JSON.stringify({
      message: userMessage,
      session_id: sessionId.value,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      isTyping.value = false
      sending.value = false

      if (data.success && data.message) {
        messages.value.push({
          role: 'assistant',
          content: data.message,
          timestamp: new Date().toISOString(),
        })
      } else {
        messages.value.push({
          role: 'assistant',
          content: data.message || 'Disculpa, estoy teniendo problemas para responder.',
          timestamp: new Date().toISOString(),
        })
      }
      saveMessages()
      scrollToBottom()
    })
    .catch((err) => {
      isTyping.value = false
      sending.value = false
      messages.value.push({
        role: 'assistant',
        content: 'Disculpa, estoy teniendo problemas para responder. Intenta de nuevo.',
        timestamp: new Date().toISOString(),
      })
      saveMessages()
      scrollToBottom()
    })
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

const formatTime = (timestamp) => {
  const date = new Date(timestamp)
  return date.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' })
}

const checkAvailability = () => {
  fetch(`/m/${props.businessSlug}/ai-chatbot/settings`)
    .then((res) => res.json())
    .then((data) => {
      isAvailable.value = data.available === true
    })
    .catch(() => {
      isAvailable.value = false
    })
}

watch(isOpen, (open) => {
  if (open) {
    loadHistory()
  }
})

onMounted(() => {
  checkAvailability()
})
</script>

<style scoped lang="less">
.ai-chat-widget {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 9999;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.chat-bubble {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
  transition: transform 0.2s, box-shadow 0.2s;
  position: relative;

  &:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
  }

  .bubble-icon {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .bubble-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #dc3545;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 6px;
  }
}

.chat-window {
  position: absolute;
  bottom: 70px;
  right: 0;
  width: 380px;
  max-width: calc(100vw - 40px);
  height: 520px;
  max-height: calc(100vh - 100px);
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.chat-header {
  padding: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #fff;

  .chat-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .chat-avatar {
    width: 42px;
    height: 42px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
  }

  .chat-title {
    font-weight: 600;
    font-size: 1rem;
  }

  .chat-status {
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 6px;
    opacity: 0.9;

    .status-dot {
      width: 8px;
      height: 8px;
      background: #4ade80;
      border-radius: 50%;
    }
  }

  .chat-close-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: #fff;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.2s;

    &:hover {
      background: rgba(255, 255, 255, 0.3);
    }
  }

  .chat-header-actions {
    display: flex;
    gap: 8px;
  }

  .chat-action-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: #fff;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.2s;

    &:hover {
      background: rgba(255, 255, 255, 0.3);
    }
  }
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  background: #f8f9fa;

  &::-webkit-scrollbar {
    width: 6px;
  }

  &::-webkit-scrollbar-track {
    background: transparent;
  }

  &::-webkit-scrollbar-thumb {
    background: #ced4da;
    border-radius: 3px;
  }

  .chat-empty {
    text-align: center;
    color: #6c757d;
    padding: 40px 20px;

    i {
      font-size: 3rem;
      margin-bottom: 12px;
      opacity: 0.3;
    }

    p {
      font-size: 0.9rem;
      margin: 0;
      line-height: 1.5;
    }
  }
}

.chat-message {
  margin-bottom: 12px;
  display: flex;
  flex-direction: column;

  &.user {
    align-items: flex-end;

    .message-content {
      background: #0d6efd;
      color: #fff;
      border-radius: 18px 18px 4px 18px;
    }
  }

  &.assistant {
    align-items: flex-start;

    .message-content {
      background: #fff;
      color: #212529;
      border-radius: 18px 18px 18px 4px;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
  }

  .message-content {
    max-width: 80%;
    padding: 10px 16px;
    font-size: 0.9rem;
    line-height: 1.5;
    word-wrap: break-word;

    &.typing {
      display: flex;
      gap: 4px;
      padding: 14px 18px;

      span {
        width: 8px;
        height: 8px;
        background: #6c757d;
        border-radius: 50%;
        animation: typing 1.4s infinite;

        &:nth-child(2) {
          animation-delay: 0.2s;
        }

        &:nth-child(3) {
          animation-delay: 0.4s;
        }
      }
    }
  }

  .message-time {
    font-size: 0.7rem;
    color: #adb5bd;
    margin-top: 4px;
    padding: 0 4px;
  }
}

@keyframes typing {
  0%, 60%, 100% {
    transform: translateY(0);
  }
  30% {
    transform: translateY(-4px);
  }
}

.chat-input {
  padding: 12px 16px;
  background: #fff;
  border-top: 1px solid #e9ecef;
  display: flex;
  gap: 10px;

  input {
    flex: 1;
    border: 1px solid #dee2e6;
    border-radius: 24px;
    padding: 12px 18px;
    font-size: 0.9rem;
    outline: none;
    transition: border-color 0.2s;

    &:focus {
      border-color: #0d6efd;
    }

    &:disabled {
      background: #e9ecef;
      cursor: not-allowed;
    }

    &::placeholder {
      color: #adb5bd;
    }
  }

  .send-btn {
    width: 44px;
    height: 44px;
    border: none;
    border-radius: 50%;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.2s;

    &:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    &:not(:disabled):hover {
      transform: scale(1.05);
    }
  }
}

@media (max-width: 480px) {
  .ai-chat-widget {
    bottom: 16px;
    right: 16px;
  }

  .chat-window {
    width: calc(100vw - 32px);
    height: calc(100vh - 120px);
    max-height: 600px;
  }
}

// Dark theme
.ai-chat-widget.dark {
  .chat-window {
    background: #1a1a2e;
    border-color: #3a3a5a;
  }

  .chat-header {
    color: #fff;
  }

  .chat-messages {
    background: #16162a;

    .chat-empty {
      color: #9ca3af;

      i {
        opacity: 0.5;
      }
    }
  }

  .chat-message {
    &.assistant {
      .message-content {
        background: #2a2a4a;
        color: #e5e5e5;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
      }
    }

    &.user {
      .message-content {
        background: var(--widget-color, #3B82F6);
        color: #fff;
      }
    }

    .message-time {
      color: #6b7280;
    }
  }

  .chat-input {
    background: #1a1a2e;
    border-top-color: #3a3a5a;

    input {
      background: #2a2a4a;
      border-color: #3a3a5a;
      color: #e5e5e5;

      &:focus {
        border-color: var(--widget-color, #3B82F6);
      }

      &::placeholder {
        color: #9ca3af;
      }
    }
  }
}
</style>
