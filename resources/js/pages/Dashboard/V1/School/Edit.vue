<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
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
import { schoolSchema } from '@school/validation/schoolSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { SchoolFormData, SchoolEditProps, SchoolType } from '@school/types';

const props = defineProps<SchoolEditProps>();

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

const form = useForm<SchoolFormData>({
    name: props.school.name,
    code: props.school.code || '',
    type: props.school.type,
    description: props.school.description || '',
    address: props.school.address || '',
    city: props.school.city || '',
    country: props.school.country || '',
    phone: props.school.phone || '',
    email: props.school.email || '',
    website: props.school.website || '',
    logo: props.school.logo || '',
    established_year: props.school.established_year,
    accreditation: props.school.accreditation || '',
    status: props.school.status,
});

const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    schoolSchema,
    ['name', 'type']
);

const getFormData = () => ({
    name: form.name,
    code: form.code || null,
    type: form.type,
    description: form.description || null,
    address: form.address || null,
    city: form.city || null,
    country: form.country || null,
    phone: form.phone || null,
    email: form.email || null,
    website: form.website || null,
    logo: form.logo || null,
    established_year: form.established_year,
    accreditation: form.accreditation || null,
    status: form.status,
});

watch(() => form.name, () => validateForm(getFormData()));

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.put(`/dashboard/schools/${props.school.id}`, {
            onSuccess: () => {
                toast.success('School updated successfully.');
                setTimeout(() => {
                    close();
                    redirect();
                }, 100);
            },
        });
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

const typeOptions = computed(() => {
    return Object.entries(props.types).map(([value, label]) => ({
        value: value as SchoolType,
        label,
    }));
});

const handleTypeChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string') {
        form.type = value as SchoolType;
    }
};
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Edit School"
        :description="`Editing: ${school.name}`"
        mode="edit"
        size="lg"
        submit-text="Save Changes"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
            <!-- Name & Code -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="name">
                        Name <span class="text-destructive">*</span>
                    </Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="Enter school name"
                        :class="{ 'border-destructive': form.errors.name }"
                    />
                    <p v-if="form.errors.name" class="text-xs text-destructive">
                        {{ form.errors.name }}
                    </p>
                </div>
                <div class="space-y-2">
                    <Label for="code">Code</Label>
                    <Input
                        id="code"
                        v-model="form.code"
                        placeholder="e.g., SCH001"
                        :class="{ 'border-destructive': form.errors.code }"
                    />
                    <p v-if="form.errors.code" class="text-xs text-destructive">
                        {{ form.errors.code }}
                    </p>
                </div>
            </div>

            <!-- Type -->
            <div class="space-y-2">
                <Label for="type">
                    Type <span class="text-destructive">*</span>
                </Label>
                <Select :model-value="form.type" @update:model-value="handleTypeChange">
                    <SelectTrigger :class="{ 'border-destructive': form.errors.type }">
                        <SelectValue placeholder="Select type" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="type in typeOptions" :key="type.value" :value="type.value">
                            {{ type.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="form.errors.type" class="text-xs text-destructive">
                    {{ form.errors.type }}
                </p>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <Label for="description">Description</Label>
                <Textarea
                    id="description"
                    v-model="form.description"
                    placeholder="Enter school description"
                    rows="3"
                />
            </div>

            <!-- Contact Info -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        v-model="form.email"
                        placeholder="school@example.com"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="phone">Phone</Label>
                    <Input
                        id="phone"
                        v-model="form.phone"
                        placeholder="+1 234 567 8900"
                    />
                </div>
            </div>

            <!-- Location -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="city">City</Label>
                    <Input
                        id="city"
                        v-model="form.city"
                        placeholder="City name"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="country">Country</Label>
                    <Input
                        id="country"
                        v-model="form.country"
                        placeholder="Country name"
                    />
                </div>
            </div>

            <!-- Website & Established Year -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="website">Website</Label>
                    <Input
                        id="website"
                        v-model="form.website"
                        placeholder="https://example.com"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="established_year">Established Year</Label>
                    <Input
                        id="established_year"
                        type="number"
                        v-model.number="form.established_year"
                        placeholder="e.g., 1990"
                    />
                </div>
            </div>

            <!-- Status -->
            <div class="flex items-center justify-between rounded-lg border p-4">
                <div>
                    <p class="text-sm font-medium">Active Status</p>
                    <p class="text-xs text-muted-foreground">
                        {{ isActive ? 'School is active' : 'School is inactive' }}
                    </p>
                </div>
                <Switch v-model="isActive" />
            </div>
        </div>
    </ModalForm>
</template>
