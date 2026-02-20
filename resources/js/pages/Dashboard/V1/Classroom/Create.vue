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
import { Checkbox } from '@/components/ui/checkbox';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { classroomSchema } from '@school/validation/classroomSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { ClassroomFormData, ClassroomCreateProps, ClassroomType } from '@school/types';

const props = defineProps<ClassroomCreateProps>();

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

const form = useForm<ClassroomFormData>({
    department_id: '',
    name: '',
    code: '',
    building: '',
    floor: null,
    capacity: 30,
    type: 'classroom',
    equipment: [],
    description: '',
    has_projector: false,
    has_whiteboard: true,
    has_ac: false,
    is_available: true,
    status: true,
});

const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    classroomSchema,
    ['department_id', 'name', 'type', 'capacity']
);

const getFormData = () => ({
    department_id: form.department_id,
    name: form.name,
    code: form.code || null,
    building: form.building || null,
    floor: form.floor,
    capacity: form.capacity,
    type: form.type,
    equipment: form.equipment,
    description: form.description || null,
    has_projector: form.has_projector,
    has_whiteboard: form.has_whiteboard,
    has_ac: form.has_ac,
    is_available: form.is_available,
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
        form.post('/dashboard/classrooms', {
            onSuccess: () => {
                toast.success('Classroom created successfully.');
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

const isAvailable = computed({
    get: () => form.is_available,
    set: (value: boolean) => {
        form.is_available = value;
    },
});

const typeOptions = computed(() => {
    return Object.entries(props.types).map(([value, label]) => ({
        value: value as ClassroomType,
        label,
    }));
});

const handleTypeChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string') {
        form.type = value as ClassroomType;
    }
};

const handleDepartmentChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string' || typeof value === 'number') {
        form.department_id = value;
    }
};

const handleEquipmentChange = (equipment: string, checked: boolean | 'indeterminate') => {
    if (checked === true) {
        if (!form.equipment.includes(equipment)) {
            form.equipment = [...form.equipment, equipment];
        }
    } else {
        form.equipment = form.equipment.filter(e => e !== equipment);
    }
};

const handleProjectorChange = (value: boolean | 'indeterminate') => {
    form.has_projector = value === true;
};

const handleWhiteboardChange = (value: boolean | 'indeterminate') => {
    form.has_whiteboard = value === true;
};

const handleAcChange = (value: boolean | 'indeterminate') => {
    form.has_ac = value === true;
};
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Create Classroom"
        description="Add a new classroom or facility"
        mode="create"
        size="lg"
        submit-text="Create Classroom"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
            <!-- Department -->
            <div class="space-y-2">
                <Label for="department_id">
                    Department <span class="text-destructive">*</span>
                </Label>
                <Select :model-value="String(form.department_id || '')" @update:model-value="handleDepartmentChange">
                    <SelectTrigger :class="{ 'border-destructive': form.errors.department_id }">
                        <SelectValue placeholder="Select department" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="dept in props.departments" :key="dept.id" :value="String(dept.id)">
                            {{ dept.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="form.errors.department_id" class="text-xs text-destructive">
                    {{ form.errors.department_id }}
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
                        placeholder="Enter classroom name"
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
                        placeholder="e.g., RM101"
                        :class="{ 'border-destructive': form.errors.code }"
                    />
                    <p v-if="form.errors.code" class="text-xs text-destructive">
                        {{ form.errors.code }}
                    </p>
                </div>
            </div>

            <!-- Type & Capacity -->
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
                    <Label for="capacity">
                        Capacity <span class="text-destructive">*</span>
                    </Label>
                    <Input
                        id="capacity"
                        type="number"
                        v-model.number="form.capacity"
                        placeholder="e.g., 30"
                        :class="{ 'border-destructive': form.errors.capacity }"
                    />
                    <p v-if="form.errors.capacity" class="text-xs text-destructive">
                        {{ form.errors.capacity }}
                    </p>
                </div>
            </div>

            <!-- Building & Floor -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="building">Building</Label>
                    <Input
                        id="building"
                        v-model="form.building"
                        placeholder="e.g., Main Building"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="floor">Floor</Label>
                    <Input
                        id="floor"
                        type="number"
                        v-model.number="form.floor"
                        placeholder="e.g., 1"
                    />
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <Label for="description">Description</Label>
                <Textarea
                    id="description"
                    v-model="form.description"
                    placeholder="Enter classroom description"
                    rows="3"
                />
            </div>

            <!-- Amenities -->
            <div class="space-y-3">
                <Label>Amenities</Label>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="has_projector"
                            :checked="form.has_projector"
                            @update:checked="handleProjectorChange"
                        />
                        <Label for="has_projector" class="cursor-pointer font-normal">
                            Projector
                        </Label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="has_whiteboard"
                            :checked="form.has_whiteboard"
                            @update:checked="handleWhiteboardChange"
                        />
                        <Label for="has_whiteboard" class="cursor-pointer font-normal">
                            Whiteboard
                        </Label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="has_ac"
                            :checked="form.has_ac"
                            @update:checked="handleAcChange"
                        />
                        <Label for="has_ac" class="cursor-pointer font-normal">
                            Air Conditioning
                        </Label>
                    </div>
                </div>
            </div>

            <!-- Equipment -->
            <div class="space-y-3">
                <Label>Additional Equipment</Label>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div v-for="equipment in props.equipmentOptions" :key="equipment.value" class="flex items-center space-x-2">
                        <Checkbox
                            :id="`equipment-${equipment.value}`"
                            :checked="form.equipment.includes(equipment.value)"
                            @update:checked="(checked) => handleEquipmentChange(equipment.value, checked)"
                        />
                        <Label :for="`equipment-${equipment.value}`" class="cursor-pointer font-normal">
                            {{ equipment.label }}
                        </Label>
                    </div>
                </div>
            </div>

            <!-- Availability & Status -->
            <div class="space-y-4">
                <div class="flex items-center justify-between rounded-lg border p-4">
                    <div>
                        <p class="text-sm font-medium">Available for Booking</p>
                        <p class="text-xs text-muted-foreground">
                            {{ isAvailable ? 'Classroom can be booked' : 'Classroom is not available for booking' }}
                        </p>
                    </div>
                    <Switch v-model="isAvailable" />
                </div>
                <div class="flex items-center justify-between rounded-lg border p-4">
                    <div>
                        <p class="text-sm font-medium">Active Status</p>
                        <p class="text-xs text-muted-foreground">
                            {{ isActive ? 'Classroom is active' : 'Classroom is inactive' }}
                        </p>
                    </div>
                    <Switch v-model="isActive" />
                </div>
            </div>
        </div>
    </ModalForm>
</template>
