<?php

namespace Modules\AiChatbot\Services;

use Modules\AiChatbot\Models\AiEmbedding;
use Modules\AiChatbot\Models\BusinessAiSetting;
use Illuminate\Support\Facades\Log;

class VectorStoreService
{
    private EmbeddingService $embeddingService;
    private BusinessAiSetting $settings;

    public function __construct(BusinessAiSetting $settings)
    {
        $this->settings = $settings;
        $this->embeddingService = new EmbeddingService(
            $settings->api_key,
            $settings->provider,
            $settings->embedding_model
        );
    }

    public function storeEmbedding(string $sourceType, int $sourceId, string $text): void
    {
        $existing = AiEmbedding::where('business_id', $this->settings->business_id)
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->first();

        if ($existing) {
            $this->updateEmbedding($existing, $text);
            return;
        }

        $this->createEmbedding($sourceType, $sourceId, $text);
    }

    public function deleteEmbedding(string $sourceType, int $sourceId): void
    {
        AiEmbedding::where('business_id', $this->settings->business_id)
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->delete();
    }

    public function searchSimilar(string $query, int $limit = 5, float $minSimilarity = 0.5): array
    {
        try {
            $queryEmbedding = $this->embeddingService->embed($query);
        } catch (\Exception $e) {
            Log::error('VectorStore search embedding error', [
                'business_id' => $this->settings->business_id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }

        $embeddings = AiEmbedding::where('business_id', $this->settings->business_id)
            ->get();

        $results = [];

        foreach ($embeddings as $embedding) {
            $storedEmbedding = $embedding->getEmbeddingArray();

            if (empty($storedEmbedding)) {
                continue;
            }

            $similarity = $this->embeddingService->cosineSimilarity($queryEmbedding, $storedEmbedding);

            if ($similarity >= $minSimilarity) {
                $results[] = [
                    'source_type' => $embedding->source_type,
                    'source_id' => $embedding->source_id,
                    'chunk_text' => $embedding->chunk_text,
                    'similarity' => round($similarity, 4),
                ];
            }
        }

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return array_slice($results, 0, $limit);
    }

    public function reindexBusiness(): array
    {
        $businessId = $this->settings->business_id;

        AiEmbedding::where('business_id', $businessId)->delete();

        $stats = [
            'products' => 0,
            'services' => 0,
            'promotions' => 0,
            'faqs' => 0,
            'locations' => 0,
            'about' => 0,
            'custom' => 0,
            'restaurant_menu' => 0,
        ];

        $stats['products'] = $this->indexProducts($businessId);
        $stats['services'] = $this->indexServices($businessId);
        $stats['promotions'] = $this->indexPromotions($businessId);
        $stats['faqs'] = $this->indexFaqs($businessId);
        $stats['locations'] = $this->indexLocations($businessId);
        $stats['about'] = $this->indexAbout($businessId);
        $stats['custom'] = $this->indexCustomContexts($businessId);
        $stats['restaurant_menu'] = $this->indexRestaurantMenu($businessId);

        return $stats;
    }

    private function createEmbedding(string $sourceType, int $sourceId, string $text): void
    {
        try {
            $embeddingArray = $this->embeddingService->embed($text);

            AiEmbedding::create([
                'business_id' => $this->settings->business_id,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'chunk_text' => $text,
                'embedding' => json_encode($embeddingArray),
            ]);
        } catch (\Exception $e) {
            Log::error('VectorStore create embedding error', [
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function updateEmbedding(AiEmbedding $embedding, string $text): void
    {
        try {
            $embeddingArray = $this->embeddingService->embed($text);
            $embedding->update([
                'chunk_text' => $text,
                'embedding' => json_encode($embeddingArray),
            ]);
        } catch (\Exception $e) {
            Log::error('VectorStore update embedding error', [
                'id' => $embedding->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function indexProducts(int $businessId): int
    {
        $products = \Modules\Products\Models\BusinessProduct::where('business_id', $businessId)
            ->where('is_active', true)
            ->get();

        foreach ($products as $product) {
            $text = implode('. ', array_filter([
                $product->name,
                $product->description,
                $product->sku ? "SKU: {$product->sku}" : null,
                $product->price ? "Precio: {$product->price}" : null,
            ]));

            if ($text) {
                $this->storeEmbedding('product', $product->id, $text);
            }
        }

        return $products->count();
    }

    private function indexServices(int $businessId): int
    {
        $services = \Modules\Services\Models\BusinessService::where('business_id', $businessId)
            ->where('is_active', true)
            ->get();

        foreach ($services as $service) {
            $text = implode('. ', array_filter([
                $service->name,
                $service->description,
                $service->duration ? "Duración: {$service->duration} minutos" : null,
                $service->price ? "Precio: {$service->price}" : null,
            ]));

            if ($text) {
                $this->storeEmbedding('service', $service->id, $text);
            }
        }

        return $services->count();
    }

    private function indexPromotions(int $businessId): int
    {
        $promotions = \Modules\Promotions\Models\BusinessPromotion::where('business_id', $businessId)
            ->where('is_active', true)
            ->get();

        foreach ($promotions as $promo) {
            $text = implode('. ', array_filter([
                $promo->title,
                $promo->description,
                $promo->terms ? "Términos: {$promo->terms}" : null,
                $promo->discount_type === 'percentage'
                    ? "Descuento: {$promo->discount_value}%"
                    : ($promo->discount_value ? "Descuento: {$promo->discount_value}" : null),
            ]));

            if ($text) {
                $this->storeEmbedding('promotion', $promo->id, $text);
            }
        }

        return $promotions->count();
    }

    private function indexFaqs(int $businessId): int
    {
        $faqs = \Modules\Faqs\Models\BusinessFaq::where('business_id', $businessId)
            ->where('is_active', true)
            ->get();

        foreach ($faqs as $faq) {
            $text = "Pregunta: {$faq->question}. Respuesta: {$faq->answer}";

            $this->storeEmbedding('faq', $faq->id, $text);
        }

        return $faqs->count();
    }

    private function indexLocations(int $businessId): int
    {
        $locations = \Modules\Locations\Models\BusinessLocation::where('business_id', $businessId)
            ->where('is_active', true)
            ->get();

        foreach ($locations as $location) {
            $text = implode('. ', array_filter([
                $location->name,
                $location->address,
                $location->phone ? "Teléfono: {$location->phone}" : null,
                $location->email ? "Email: {$location->email}" : null,
                $location->hours ? "Horarios: {$location->hours}" : null,
            ]));

            if ($text) {
                $this->storeEmbedding('location', $location->id, $text);
            }
        }

        return $locations->count();
    }

    private function indexAbout(int $businessId): int
    {
        $abouts = \Modules\About\Models\BusinessAbout::where('business_id', $businessId)
            ->get();

        foreach ($abouts as $about) {
            $text = "Acerca de: {$about->content}";

            $this->storeEmbedding('about', $about->id, $text);
        }

        return $abouts->count();
    }

    private function indexCustomContexts(int $businessId): int
    {
        $contexts = \Modules\AiChatbot\Models\AiContext::where('business_id', $businessId)
            ->where('is_active', true)
            ->get();

        foreach ($contexts as $context) {
            $text = "{$context->title}. {$context->content}";

            $this->storeEmbedding('custom', $context->id, $text);
        }

        return $contexts->count();
    }

    private function indexRestaurantMenu(int $businessId): int
    {
        $count = 0;

        $categories = \Modules\RestaurantMenu\Entities\MenuCategory::where('business_id', $businessId)
            ->where('active', true)
            ->with(['activeProducts.variants', 'children.activeProducts.variants'])
            ->whereNull('parent_id')
            ->get();

        foreach ($categories as $category) {
            $categoryText = "Categoría del menú: {$category->title}";
            if ($category->description) {
                $categoryText .= ". {$category->description}";
            }
            $this->storeEmbedding('restaurant_category', $category->id, $categoryText);
            $count++;

            foreach ($category->activeProducts as $product) {
                $productText = "Producto del menú: {$product->title}";
                if ($product->description) {
                    $productText .= ". {$product->description}";
                }
                if ($product->base_price) {
                    $productText .= ". Precio: {$product->base_price}";
                }

                $variantTexts = [];
                foreach ($product->activeVariants as $variant) {
                    $variantText = "{$variant->title}";
                    if ($variant->description) {
                        $variantText .= " - {$variant->description}";
                    }
                    if ($variant->price) {
                        $variantText .= " - Precio: {$variant->price}";
                    }
                    $variantTexts[] = $variantText;
                }
                if ($variantTexts) {
                    $productText .= ". Variantes: " . implode('. ', $variantTexts);
                }

                $this->storeEmbedding('restaurant_product', $product->id, $productText);
                $count++;
            }

            foreach ($category->children as $child) {
                $childText = "Subcategoría del menú: {$child->title}";
                if ($child->description) {
                    $childText .= ". {$child->description}";
                }
                $this->storeEmbedding('restaurant_category', $child->id, $childText);
                $count++;

                foreach ($child->activeProducts as $product) {
                    $productText = "Producto del menú: {$product->title}";
                    if ($product->description) {
                        $productText .= ". {$product->description}";
                    }
                    if ($product->base_price) {
                        $productText .= ". Precio: {$product->base_price}";
                    }

                    $variantTexts = [];
                    foreach ($product->activeVariants as $variant) {
                        $variantText = "{$variant->title}";
                        if ($variant->description) {
                            $variantText .= " - {$variant->description}";
                        }
                        if ($variant->price) {
                            $variantText .= " - Precio: {$variant->price}";
                        }
                        $variantTexts[] = $variantText;
                    }
                    if ($variantTexts) {
                        $productText .= ". Variantes: " . implode('. ', $variantTexts);
                    }

                    $this->storeEmbedding('restaurant_product', $product->id, $productText);
                    $count++;
                }
            }
        }

        return $count;
    }
}
