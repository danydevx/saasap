<template>
  <div class="form-group" :class="classObject">
    <div class="form-floating">
      <input
        :id="id"
        type="time"
        v-model="inputValue"
        class="form-control"
        :readonly="readonly"
        :disabled="readonly"
        :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
        @blur="onBlur"
      />
      <label :for="id">
        {{ label }} <strong v-if="required">*</strong>
      </label>

      <div v-if="(showValidation && validationMessage) || formError" class="invalid-feedback">
        {{ formError || validationMessage }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "FieldTime",

  props: {
    id: { type: String, required: true },
    label: { type: String, required: true },
    modelValue: { type: String, default: "" }, // formato esperado "HH:MM"
    required: { type: Boolean, default: false },
    showValidation: { type: Boolean, default: false },
    formError: { type: String, default: "" },

    validateFunction: { type: Function, default: null },

    classObject: { type: [String, Object, Array], default: "" },

    readonly: { type: Boolean, default: false }
  },

  emits: ["update:modelValue", "blur"],

  computed: {
    inputValue: {
      get() {
        return this.modelValue ?? "";
      },
      set(val) {
        if (this.readonly) return;
        this.$emit("update:modelValue", val);
      }
    },

    validationMessage() {
      // Validación personalizada
      if (this.validateFunction) return this.validateFunction();

      // Validación mínima
      if (this.required && (!this.modelValue || !this.modelValue.trim())) {
        return "Este campo es requerido";
      }

      // Validar formato HH:MM
      if (this.modelValue) {
        const regex = /^([01]\d|2[0-3]):([0-5]\d)$/;
        if (!regex.test(this.modelValue)) return "Hora inválida";
      }

      return "";
    }
  },

  methods: {
    onBlur() {
      this.$emit("blur");
    }
  }
};
</script>

<style scoped>
.form-group {
  width: 100%;
}
</style>
