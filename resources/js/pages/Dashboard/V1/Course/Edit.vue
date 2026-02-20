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
import { courseSchema } from '@school/validation/courseSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { CourseFormData, CourseEditProps, CourseType } from '@school/types';

const props = defineProps<CourseEditProps>();

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

const form = useForm<CourseFormData>({
    department_id: props.course.department_id,
    program_id: props.course.program_id,
    instructor_id: props.course.instructor_id,
    name: props.course.name,
    code: props.course.code || '',
    description: props.course.description || '',
    credits: props.course.credits,
    type: props.course.type,
    semester: props.course.semester,
    year: props.course.year,
    max_students: props.course.max_students,
    current_enrollment: props.course.current_enrollment,
    schedule: props.course.schedule || '',
    room: props.course.room || '',
    prerequisites: props.course.prerequisites || [],
    syllabus: props.course.syllabus || '',
    status: props.course.status,
});

const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    courseSchema,
    ['name', 'credits', 'type']
);

const getFormData = () => ({
    department_id: form.department_id,
    program_id: form.program_id,
    instructor_id: form.instructor_id,
    name: form.name,
    code: form.code || null,
    description: form.description || null,
    credits: form.credits,
    type: form.type,
    semester: form.semester,
    year: form.year,
    max_students: form.max_students,
    current_enrollment: form.current_enrollment,
    schedule: form.schedule || null,
    room: form.room || null,
    prerequisites: form.prerequisites.length > 0 ? form.prerequisites : null,
    syllabus: form.syllabus || null,
    status: form.status,
});

watch(() => form.name, () => validateForm(getFormData()));

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.put(`/dashboard/courses/${props.course.id}`, {
            onSuccess: () => {
                toast.success('Course updated successfully.');
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
        value: value as CourseType,
        label,
    }));
});

const handleTypeChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string') {
        form.type = value as CourseType;
    }
};

const handleDepartmentChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (value === 'none') {
        form.department_id = null;
    } else if (typeof value === 'string') {
        form.department_id = parseInt(value);
    }
};

const handleProgramChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (value === 'none') {
        form.program_id = null;
    } else if (typeof value === 'string') {
        form.program_id = parseInt(value);
    }
};
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Edit Course"
        :description="`Editing: ${course.name}`"
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
                        placeholder="Enter course name"
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
                        placeholder="e.g., CS101"
                        :class="{ 'border-destructive': form.errors.code }"
                    />
                    <p v-if="form.errors.code" class="text-xs text-destructive">
                        {{ form.errors.code }}
                    </p>
                </div>
            </div>

            <!-- Type & Credits -->
            <div class="grid gap-4 sm:grid-cols-2">
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
                <div class="space-y-2">
                    <Label for="credits">
                        Credits <span class="text-destructive">*</span>
                    </Label>
                    <Input
                        id="credits"
                        type="number"
                        v-model.number="form.credits"
                        min="1"
                        max="20"
                        :class="{ 'border-destructive': form.errors.credits }"
                    />
                    <p v-if="form.errors.credits" class="text-xs text-destructive">
                        {{ form.errors.credits }}
                    </p>
                </div>
            </div>

            <!-- Department & Program -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="department_id">Department</Label>
                    <Select :model-value="form.department_id?.toString() || 'none'" @update:model-value="handleDepartmentChange">
                        <SelectTrigger>
                            <SelectValue placeholder="Select department" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="none">No Department</SelectItem>
                            <SelectItem v-for="dept in props.departments" :key="dept.id" :value="dept.id.toString()">
                                {{ dept.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="space-y-2">
                    <Label for="program_id">Program</Label>
                    <Select :model-value="form.program_id?.toString() || 'none'" @update:model-value="handleProgramChange">
                        <SelectTrigger>
                            <SelectValue placeholder="Select program" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="none">No Program</SelectItem>
                            <SelectItem v-for="prog in props.programs" :key="prog.id" :value="prog.id.toString()">
                                {{ prog.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <Label for="description">Description</Label>
                <Textarea
                    id="description"
                    v-model="form.description"
                    placeholder="Enter course description"
                    rows="3"
                />
            </div>

            <!-- Semester & Year -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="semester">Semester</Label>
                    <Input
                        id="semester"
                        type="number"
                        v-model.number="form.semester"
                        min="1"
                        max="8"
                        placeholder="e.g., 1"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="year">Year</Label>
                    <Input
                        id="year"
                        type="number"
                        v-model.number="form.year"
                        min="1"
                        max="10"
                        placeholder="e.g., 1"
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
                        min="1"
                        placeholder="e.g., 30"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="current_enrollment">Current Enrollment</Label>
                    <Input
                        id="current_enrollment"
                        type="number"
                        v-model.number="form.current_enrollment"
                        min="0"
                        placeholder="e.g., 0"
                    />
                </div>
            </div>

            <!-- Schedule & Room -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="schedule">Schedule</Label>
                    <Input
                        id="schedule"
                        v-model="form.schedule"
                        placeholder="e.g., Mon/Wed 9:00-10:30"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="room">Room</Label>
                    <Input
                        id="room"
                        v-model="form.room"
                        placeholder="e.g., Room 101"
                    />
                </div>
            </div>

            <!-- Status -->
            <div class="flex items-center justify-between rounded-lg border p-4">
                <div>
                    <p class="text-sm font-medium">Active Status</p>
                    <p class="text-xs text-muted-foreground">
                        {{ isActive ? 'Course is active' : 'Course is inactive' }}
                    </p>
                </div>
                <Switch v-model="isActive" />
            </div>
        </div>
    </ModalForm>
</template>
