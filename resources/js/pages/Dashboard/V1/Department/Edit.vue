<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import DepartmentForm from '../../../../Components/Dashboard/V1/DepartmentForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
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
        <DepartmentForm
            v-model="form"
            :schools="props.schools"
            mode="edit"
        />
    </ModalForm>
</template>
