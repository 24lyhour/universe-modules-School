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
import { ImageUpload } from '@/components/shared';
import type { InertiaForm } from '@inertiajs/vue3';
import type { SchoolFormData, SchoolType } from '@school/types';

interface Props {
    mode?: 'create' | 'edit';
    types: Record<SchoolType, string>;
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
});

const model = defineModel<InertiaForm<SchoolFormData>>({ required: true });

const typeOptions = computed(() => {
    return Object.entries(props.types).map(([value, label]) => ({
        value: value as SchoolType,
        label,
    }));
});

// Convert type to string for Select component
const typeString = computed({
    get: () => model.value.type,
    set: (val: string) => {
        model.value.type = val as SchoolType;
    },
});

// Convert status to boolean
const isActive = computed({
    get: () => model.value.status,
    set: (val: boolean) => {
        model.value.status = val;
    },
});

// Nullable numbers workaround
const establishedYearValue = computed({
    get: () => model.value.established_year ?? undefined,
    set: (val: string | number | undefined | null) => {
        model.value.established_year = val ? Number(val) : null;
    }
});

// Logo - convert string to array for ImageUpload component
const logoImages = computed({
    get: () => model.value.logo ? [model.value.logo] : [],
    set: (val: string[]) => {
        model.value.logo = val.length > 0 ? val[0] : '';
    }
});
</script>

<template>
    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
        <!-- Name & Code -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="name">
                    Name <span class="text-destructive">*</span>
                </Label>
                <Input
                    id="name"
                    v-model="model.name"
                    placeholder="Enter school name"
                    :class="{ 'border-destructive': model.errors.name }"
                />
                <p v-if="model.errors.name" class="text-xs text-destructive">
                    {{ model.errors.name }}
                </p>
            </div>
            <div class="space-y-2">
                <Label for="code">Code</Label>
                <Input
                    id="code"
                    v-model="model.code"
                    placeholder="e.g., SCH001"
                    :class="{ 'border-destructive': model.errors.code }"
                />
                <p v-if="model.errors.code" class="text-xs text-destructive">
                    {{ model.errors.code }}
                </p>
            </div>
        </div>

        <!-- Type -->
        <div class="space-y-2">
            <Label for="type">
                Type <span class="text-destructive">*</span>
            </Label>
            <Select v-model="typeString">
                <SelectTrigger :class="{ 'border-destructive': model.errors.type }">
                    <SelectValue placeholder="Select type" />
                </SelectTrigger>
                <SelectContent class="z-200">
                    <SelectItem v-for="type in typeOptions" :key="type.value" :value="type.value">
                        {{ type.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="model.errors.type" class="text-xs text-destructive">
                {{ model.errors.type }}
            </p>
        </div>

        <!-- Description -->
        <div class="space-y-2">
            <Label for="description">Description</Label>
            <Textarea
                id="description"
                v-model="model.description"
                placeholder="Enter school description"
                rows="3"
            />
            <p v-if="model.errors.description" class="text-xs text-destructive">
                {{ model.errors.description }}
            </p>
        </div>

        <!-- Logo -->
        <ImageUpload
            v-model="logoImages"
            label="Logo"
            :multiple="false"
            :max-files="1"
            :max-size="5"
            :error="model.errors.logo"
        />

        <!-- Contact Info -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    type="email"
                    v-model="model.email"
                    placeholder="school@example.com"
                />
                <p v-if="model.errors.email" class="text-xs text-destructive">
                    {{ model.errors.email }}
                </p>
            </div>
            <div class="space-y-2">
                <Label for="phone">Phone</Label>
                <Input
                    id="phone"
                    v-model="model.phone"
                    placeholder="+1 234 567 8900"
                />
                <p v-if="model.errors.phone" class="text-xs text-destructive">
                    {{ model.errors.phone }}
                </p>
            </div>
        </div>

        <!-- Location -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="city">City</Label>
                <Input
                    id="city"
                    v-model="model.city"
                    placeholder="City name"
                />
                <p v-if="model.errors.city" class="text-xs text-destructive">
                    {{ model.errors.city }}
                </p>
            </div>
            <div class="space-y-2">
                <Label for="country">Country</Label>
                <Input
                    id="country"
                    v-model="model.country"
                    placeholder="Country name"
                />
                <p v-if="model.errors.country" class="text-xs text-destructive">
                    {{ model.errors.country }}
                </p>
            </div>
        </div>

        <!-- Website & Established Year -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="website">Website</Label>
                <Input
                    id="website"
                    v-model="model.website"
                    placeholder="https://example.com"
                />
                <p v-if="model.errors.website" class="text-xs text-destructive">
                    {{ model.errors.website }}
                </p>
            </div>
            <div class="space-y-2">
                <Label for="established_year">Established Year</Label>
                <Input
                    id="established_year"
                    type="number"
                    v-model.number="establishedYearValue"
                    placeholder="e.g., 1990"
                />
                <p v-if="model.errors.established_year" class="text-xs text-destructive">
                    {{ model.errors.established_year }}
                </p>
            </div>
        </div>

        <!-- Status -->
        <div class="flex items-center justify-between rounded-lg border p-4">
            <div>
                <p class="text-sm font-medium">Active Status</p>
                <p class="text-xs text-muted-foreground">
                    {{ isActive ? 'School will be active' : 'School will be inactive' }}
                </p>
            </div>
            <Switch v-model="isActive" />
        </div>
    </div>
</template>
