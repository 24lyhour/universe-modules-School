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
import { programSchema } from '@school/validation/programSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { ProgramFormData, ProgramCreateProps, DegreeLevel } from '@school/types';

const props = defineProps<ProgramCreateProps>();

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

const form = useForm<ProgramFormData>({
    school_id: null,
    department_id: null,
    name: '',
    code: '',
    description: '',
    degree_level: 'bachelor',
    duration_years: null,
    credits_required: null,
    tuition_fee: null,
    admission_requirements: '',
    program_coordinator: '',
    max_students: null,
    current_enrollment: null,
    accreditation_status: '',
    status: true,
});

const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    programSchema,
    ['name', 'degree_level', 'school_id']
);

const getFormData = () => ({
    school_id: form.school_id,
    department_id: form.department_id || null,
    name: form.name,
    code: form.code || null,
    description: form.description || null,
    degree_level: form.degree_level,
    duration_years: form.duration_years,
    credits_required: form.credits_required,
    tuition_fee: form.tuition_fee,
    admission_requirements: form.admission_requirements || null,
    program_coordinator: form.program_coordinator || null,
    max_students: form.max_students,
    current_enrollment: form.current_enrollment,
    accreditation_status: form.accreditation_status || null,
    status: form.status,
});

watch(() => form.name, () => {
    if (form.name) {
        validateForm(getFormData());
    }
});

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.post('/dashboard/programs', {
            onSuccess: () => {
                toast.success('Program created successfully.');
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

const degreeLevelOptions = computed(() => {
    return Object.entries(props.degreeLevels).map(([value, label]) => ({
        value: value as DegreeLevel,
        label,
    }));
});

const schoolOptions = computed(() => {
    return props.schools.map((school) => ({
        value: school.id,
        label: school.name,
    }));
});

const filteredDepartments = computed(() => {
    if (!form.school_id) return [];
    return props.departments.filter((dept) => dept.school_id === form.school_id);
});

const handleDegreeLevelChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string') {
        form.degree_level = value as DegreeLevel;
    }
};

const handleSchoolChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'number') {
        form.school_id = value;
        form.department_id = null;
    } else if (typeof value === 'string') {
        form.school_id = parseInt(value, 10);
        form.department_id = null;
    }
};

const handleDepartmentChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'number') {
        form.department_id = value;
    } else if (typeof value === 'string') {
        form.department_id = parseInt(value, 10);
    }
};
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Create Program"
        description="Add a new academic program"
        mode="create"
        size="lg"
        submit-text="Create Program"
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
                        placeholder="Enter program name"
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
                        placeholder="e.g., CS-BSC-001"
                        :class="{ 'border-destructive': form.errors.code }"
                    />
                    <p v-if="form.errors.code" class="text-xs text-destructive">
                        {{ form.errors.code }}
                    </p>
                </div>
            </div>

            <!-- School & Department -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="school_id">
                        School <span class="text-destructive">*</span>
                    </Label>
                    <Select :model-value="form.school_id?.toString()" @update:model-value="handleSchoolChange">
                        <SelectTrigger :class="{ 'border-destructive': form.errors.school_id }">
                            <SelectValue placeholder="Select school" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="school in schoolOptions" :key="school.value" :value="school.value.toString()">
                                {{ school.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="form.errors.school_id" class="text-xs text-destructive">
                        {{ form.errors.school_id }}
                    </p>
                </div>
                <div class="space-y-2">
                    <Label for="department_id">Department</Label>
                    <Select :model-value="form.department_id?.toString()" @update:model-value="handleDepartmentChange" :disabled="!form.school_id || filteredDepartments.length === 0">
                        <SelectTrigger>
                            <SelectValue :placeholder="!form.school_id ? 'Select school first' : 'Select department'" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="dept in filteredDepartments" :key="dept.id" :value="dept.id.toString()">
                                {{ dept.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <!-- Degree Level -->
            <div class="space-y-2">
                <Label for="degree_level">
                    Degree Level <span class="text-destructive">*</span>
                </Label>
                <Select :model-value="form.degree_level" @update:model-value="handleDegreeLevelChange">
                    <SelectTrigger :class="{ 'border-destructive': form.errors.degree_level }">
                        <SelectValue placeholder="Select degree level" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="level in degreeLevelOptions" :key="level.value" :value="level.value">
                            {{ level.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="form.errors.degree_level" class="text-xs text-destructive">
                    {{ form.errors.degree_level }}
                </p>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <Label for="description">Description</Label>
                <Textarea
                    id="description"
                    v-model="form.description"
                    placeholder="Enter program description"
                    rows="3"
                />
            </div>

            <!-- Duration & Credits -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="duration_years">Duration (Years)</Label>
                    <Input
                        id="duration_years"
                        type="number"
                        v-model.number="form.duration_years"
                        placeholder="e.g., 4"
                        min="1"
                        max="10"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="credits_required">Credits Required</Label>
                    <Input
                        id="credits_required"
                        type="number"
                        v-model.number="form.credits_required"
                        placeholder="e.g., 120"
                        min="0"
                    />
                </div>
            </div>

            <!-- Tuition & Coordinator -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="tuition_fee">Tuition Fee</Label>
                    <Input
                        id="tuition_fee"
                        type="number"
                        v-model.number="form.tuition_fee"
                        placeholder="e.g., 15000.00"
                        min="0"
                        step="0.01"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="program_coordinator">Program Coordinator</Label>
                    <Input
                        id="program_coordinator"
                        v-model="form.program_coordinator"
                        placeholder="Coordinator name"
                    />
                </div>
            </div>

            <!-- Max Students & Current Enrollment -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="max_students">Max Students</Label>
                    <Input
                        id="max_students"
                        type="number"
                        v-model.number="form.max_students"
                        placeholder="e.g., 100"
                        min="0"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="current_enrollment">Current Enrollment</Label>
                    <Input
                        id="current_enrollment"
                        type="number"
                        v-model.number="form.current_enrollment"
                        placeholder="e.g., 75"
                        min="0"
                    />
                </div>
            </div>

            <!-- Admission Requirements -->
            <div class="space-y-2">
                <Label for="admission_requirements">Admission Requirements</Label>
                <Textarea
                    id="admission_requirements"
                    v-model="form.admission_requirements"
                    placeholder="Enter admission requirements"
                    rows="2"
                />
            </div>

            <!-- Accreditation Status -->
            <div class="space-y-2">
                <Label for="accreditation_status">Accreditation Status</Label>
                <Input
                    id="accreditation_status"
                    v-model="form.accreditation_status"
                    placeholder="e.g., Fully Accredited"
                />
            </div>

            <!-- Status -->
            <div class="flex items-center justify-between rounded-lg border p-4">
                <div>
                    <p class="text-sm font-medium">Active Status</p>
                    <p class="text-xs text-muted-foreground">
                        {{ isActive ? 'Program will be active' : 'Program will be inactive' }}
                    </p>
                </div>
                <Switch v-model="isActive" />
            </div>
        </div>
    </ModalForm>
</template>
