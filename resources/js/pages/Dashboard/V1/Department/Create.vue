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
import { departmentSchema } from '@school/validation/departmentSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { DepartmentFormData, DepartmentCreateProps } from '@school/types';

const props = defineProps<DepartmentCreateProps>();

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

const form = useForm<DepartmentFormData>({
    school_id: '',
    name: '',
    code: '',
    description: '',
    head_of_department: '',
    email: '',
    phone: '',
    office_location: '',
    established_year: null,
    total_staff: null,
    total_students: null,
    status: true,
});

// Auto-generate code from name
const generateCode = (name: string): string => {
    if (!name) return '';
    // Take first 4 letters uppercase + random 3 digits
    const prefix = name
        .replace(/[^a-zA-Z]/g, '')
        .substring(0, 4)
        .toUpperCase();
    const suffix = Math.floor(100 + Math.random() * 900).toString();
    return `${prefix}-${suffix}`;
};

// Watch name and auto-generate code if code is empty
watch(() => form.name, (newName) => {
    if (newName && !form.code) {
        form.code = generateCode(newName);
    }
});

const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    departmentSchema,
    ['name', 'school_id']
);

const getFormData = () => ({
    school_id: form.school_id,
    name: form.name,
    code: form.code || null,
    description: form.description || null,
    head_of_department: form.head_of_department || null,
    email: form.email || null,
    phone: form.phone || null,
    office_location: form.office_location || null,
    established_year: form.established_year,
    total_staff: form.total_staff,
    total_students: form.total_students,
    status: form.status,
});

watch([() => form.name, () => form.school_id], () => {
    if (form.name || form.school_id) {
        validateForm(getFormData());
    }
});

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.post('/dashboard/departments', {
            onSuccess: () => {
                toast.success('Department created successfully.');
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

const handleSchoolChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string') {
        form.school_id = value;
    }
};
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Create Department"
        description="Add a new department to a school"
        mode="create"
        size="lg"
        submit-text="Create Department"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
            <!-- School Selection -->
            <div class="space-y-2">
                <Label for="school_id">
                    School <span class="text-destructive">*</span>
                </Label>
                <Select :model-value="form.school_id" @update:model-value="handleSchoolChange">
                    <SelectTrigger :class="{ 'border-destructive': form.errors.school_id }">
                        <SelectValue placeholder="Select a school" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="school in props.schools" :key="school.value" :value="school.value">
                            {{ school.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="form.errors.school_id" class="text-xs text-destructive">
                    {{ form.errors.school_id }}
                </p>
            </div>

            <!-- Name & Code -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="name">
                        Name <span class="text-destructive">*</span>
                    </Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="Enter department name"
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
                        placeholder="e.g., DEPT001"
                        :class="{ 'border-destructive': form.errors.code }"
                    />
                    <p v-if="form.errors.code" class="text-xs text-destructive">
                        {{ form.errors.code }}
                    </p>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <Label for="description">Description</Label>
                <Textarea
                    id="description"
                    v-model="form.description"
                    placeholder="Enter department description"
                    rows="3"
                />
            </div>

            <!-- Head of Department & Office Location -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="head_of_department">Head of Department</Label>
                    <Input
                        id="head_of_department"
                        v-model="form.head_of_department"
                        placeholder="Enter head name"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="office_location">Office Location</Label>
                    <Input
                        id="office_location"
                        v-model="form.office_location"
                        placeholder="e.g., Building A, Room 101"
                    />
                </div>
            </div>

            <!-- Contact Info -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        v-model="form.email"
                        placeholder="department@example.com"
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

            <!-- Established Year & Stats -->
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="space-y-2">
                    <Label for="established_year">Established Year</Label>
                    <Input
                        id="established_year"
                        type="number"
                        v-model.number="form.established_year"
                        placeholder="e.g., 1990"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="total_staff">Total Staff</Label>
                    <Input
                        id="total_staff"
                        type="number"
                        v-model.number="form.total_staff"
                        placeholder="0"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="total_students">Total Students</Label>
                    <Input
                        id="total_students"
                        type="number"
                        v-model.number="form.total_students"
                        placeholder="0"
                    />
                </div>
            </div>

            <!-- Status -->
            <div class="flex items-center justify-between rounded-lg border p-4">
                <div>
                    <p class="text-sm font-medium">Active Status</p>
                    <p class="text-xs text-muted-foreground">
                        {{ isActive ? 'Department will be active' : 'Department will be inactive' }}
                    </p>
                </div>
                <Switch v-model="isActive" />
            </div>
        </div>
    </ModalForm>
</template>
