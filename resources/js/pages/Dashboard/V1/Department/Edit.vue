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
import type { DepartmentFormData, DepartmentEditProps } from '@school/types';

const props = defineProps<DepartmentEditProps>();

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
    school_id: String(props.department.school_id),
    name: props.department.name,
    code: props.department.code || '',
    description: props.department.description || '',
    head_of_department: props.department.head_of_department || '',
    email: props.department.email || '',
    phone: props.department.phone || '',
    office_location: props.department.office_location || '',
    established_year: props.department.established_year,
    total_staff: props.department.total_staff,
    total_students: props.department.total_students,
    status: props.department.status,
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

watch([() => form.name, () => form.school_id], () => validateForm(getFormData()));

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.put(`/dashboard/departments/${props.department.id}`, {
            onSuccess: () => {
                toast.success('Department updated successfully.');
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
        title="Edit Department"
        :description="`Editing: ${department.name}`"
        mode="edit"
        size="lg"
        submit-text="Save Changes"
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
                        {{ isActive ? 'Department is active' : 'Department is inactive' }}
                    </p>
                </div>
                <Switch v-model="isActive" />
            </div>
        </div>
    </ModalForm>
</template>
