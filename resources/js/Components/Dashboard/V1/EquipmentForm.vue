<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { InertiaForm } from '@inertiajs/vue3';
import type { EquipmentFormData, EquipmentCategory } from '@school/types';

interface Props {
    mode?: 'create' | 'edit';
    categories: Record<EquipmentCategory, string>;
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
});

const model = defineModel<InertiaForm<EquipmentFormData>>({ required: true });

const categoryOptions = computed(() => {
    return Object.entries(props.categories).map(([value, label]) => ({
        value: value as EquipmentCategory,
        label,
    }));
});

const handleCategoryChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string') {
        model.value.category = value as EquipmentCategory;
    }
};

const isActive = computed({
    get: () => model.value.status,
    set: (value: boolean) => {
        model.value.status = value;
    },
});
</script>

<template>
    <div class="space-y-4">
        <!-- Name -->
        <div class="space-y-2">
            <Label for="name">
                Name <span class="text-destructive">*</span>
            </Label>
            <Input
                id="name"
                v-model="model.name"
                placeholder="e.g., Projector"
                :class="{ 'border-destructive': model.errors.name }"
            />
            <p v-if="model.errors.name" class="text-xs text-destructive">
                {{ model.errors.name }}
            </p>
        </div>

        <!-- Slug -->
        <div class="space-y-2">
            <Label for="slug">Slug</Label>
            <Input
                id="slug"
                v-model="model.slug"
                placeholder="e.g., projector (auto-generated if empty)"
                :class="{ 'border-destructive': model.errors.slug }"
            />
            <p v-if="model.errors.slug" class="text-xs text-destructive">
                {{ model.errors.slug }}
            </p>
        </div>

        <!-- Category -->
        <div class="space-y-2">
            <Label for="category">
                Category <span class="text-destructive">*</span>
            </Label>
            <Select :model-value="model.category" @update:model-value="handleCategoryChange">
                <SelectTrigger :class="{ 'border-destructive': model.errors.category }">
                    <SelectValue placeholder="Select category" />
                </SelectTrigger>
                <SelectContent class="z-200">
                    <SelectItem v-for="cat in categoryOptions" :key="cat.value" :value="cat.value">
                        {{ cat.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="model.errors.category" class="text-xs text-destructive">
                {{ model.errors.category }}
            </p>
        </div>

        <!-- Icon -->
        <div class="space-y-2">
            <Label for="icon">Icon (Lucide icon name)</Label>
            <Input
                id="icon"
                v-model="model.icon"
                placeholder="e.g., monitor, projector"
            />
            <p v-if="model.errors.icon" class="text-xs text-destructive">
                {{ model.errors.icon }}
            </p>
        </div>

        <!-- Description -->
        <div class="space-y-2">
            <Label for="description">Description</Label>
            <Textarea
                id="description"
                v-model="model.description"
                placeholder="Enter equipment description"
                rows="3"
            />
            <p v-if="model.errors.description" class="text-xs text-destructive">
                {{ model.errors.description }}
            </p>
        </div>

        <!-- Status -->
        <div class="flex items-center justify-between rounded-lg border p-4">
            <div>
                <p class="text-sm font-medium">Active Status</p>
                <p class="text-xs text-muted-foreground">
                    {{ isActive ? 'Equipment is active and available' : 'Equipment is inactive' }}
                </p>
            </div>
            <Switch v-model="isActive" />
        </div>
    </div>
</template>
