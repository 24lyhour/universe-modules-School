<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
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
import type { EquipmentFormData, EquipmentEditProps, EquipmentCategory } from '@school/types';

const props = defineProps<EquipmentEditProps>();

const { show, close, redirect } = useModal();

const isOpen = computed({
    get: () => show.value,
    set: (val: boolean) => {
        if (!val) {
            close();
            redirect();
        }
    },
});

const form = useForm<EquipmentFormData>({
    name: props.equipment.name,
    slug: props.equipment.slug || '',
    icon: props.equipment.icon || '',
    description: props.equipment.description || '',
    category: props.equipment.category,
    status: props.equipment.status,
});

const isFormInvalid = computed(() => {
    return !form.name || !form.category;
});

const handleSubmit = () => {
    form.put(`/dashboard/equipment/${props.equipment.id}`, {
        onSuccess: () => {
            toast.success('Equipment updated successfully.');
            setTimeout(() => {
                close();
                redirect();
            }, 100);
        },
    });
};

const handleCancel = () => {
    close();
    redirect();
};

const isActive = computed({
    get: () => form.status,
    set: (value: boolean) => {
        form.status = value;
    },
});

const categoryOptions = computed(() => {
    return Object.entries(props.categories).map(([value, label]) => ({
        value: value as EquipmentCategory,
        label,
    }));
});

const handleCategoryChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string') {
        form.category = value as EquipmentCategory;
    }
};
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Edit Equipment"
        :description="`Editing: ${equipment.name}`"
        mode="edit"
        size="md"
        submit-text="Save Changes"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <div class="space-y-4">
            <!-- Name -->
            <div class="space-y-2">
                <Label for="name">
                    Name <span class="text-destructive">*</span>
                </Label>
                <Input
                    id="name"
                    v-model="form.name"
                    placeholder="e.g., Projector"
                    :class="{ 'border-destructive': form.errors.name }"
                />
                <p v-if="form.errors.name" class="text-xs text-destructive">
                    {{ form.errors.name }}
                </p>
            </div>

            <!-- Slug -->
            <div class="space-y-2">
                <Label for="slug">Slug</Label>
                <Input
                    id="slug"
                    v-model="form.slug"
                    placeholder="e.g., projector"
                    :class="{ 'border-destructive': form.errors.slug }"
                />
                <p v-if="form.errors.slug" class="text-xs text-destructive">
                    {{ form.errors.slug }}
                </p>
            </div>

            <!-- Category -->
            <div class="space-y-2">
                <Label for="category">
                    Category <span class="text-destructive">*</span>
                </Label>
                <Select :model-value="form.category" @update:model-value="handleCategoryChange">
                    <SelectTrigger :class="{ 'border-destructive': form.errors.category }">
                        <SelectValue placeholder="Select category" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="cat in categoryOptions" :key="cat.value" :value="cat.value">
                            {{ cat.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="form.errors.category" class="text-xs text-destructive">
                    {{ form.errors.category }}
                </p>
            </div>

            <!-- Icon -->
            <div class="space-y-2">
                <Label for="icon">Icon (Lucide icon name)</Label>
                <Input
                    id="icon"
                    v-model="form.icon"
                    placeholder="e.g., monitor, projector"
                />
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <Label for="description">Description</Label>
                <Textarea
                    id="description"
                    v-model="form.description"
                    placeholder="Enter equipment description"
                    rows="3"
                />
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
    </ModalForm>
</template>
