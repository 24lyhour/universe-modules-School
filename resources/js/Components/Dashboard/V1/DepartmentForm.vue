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
import type { DepartmentFormData, SchoolOption } from '@school/types';

interface Props {
    mode?: 'create' | 'edit';
    schools: SchoolOption[];
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
    schools: () => [],
});

const model = defineModel<InertiaForm<DepartmentFormData>>({ required: true });

// Convert school_id to string for Select component
const schoolIdString = computed({
    get: () => model.value.school_id ? String(model.value.school_id) : '',
    set: (val: string) => {
        model.value.school_id = val;
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

const totalStaffValue = computed({
    get: () => model.value.total_staff ?? undefined,
    set: (val: string | number | undefined | null) => {
        model.value.total_staff = val ? Number(val) : null;
    }
});

const totalStudentsValue = computed({
    get: () => model.value.total_students ?? undefined,
    set: (val: string | number | undefined | null) => {
        model.value.total_students = val ? Number(val) : null;
    }
});
</script>

<template>
    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
        <!-- School Selection -->
        <div class="space-y-2">
            <Label for="school_id">
                School <span class="text-destructive">*</span>
            </Label>
            <Select v-model="schoolIdString">
                <SelectTrigger :class="{ 'border-destructive': model.errors.school_id }">
                    <SelectValue placeholder="Select a school" />
                </SelectTrigger>
                <SelectContent class="z-200">
                    <SelectItem v-for="school in props.schools" :key="school.value" :value="String(school.value)">
                        {{ school.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="model.errors.school_id" class="text-xs text-destructive">
                {{ model.errors.school_id }}
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
                    v-model="model.name"
                    placeholder="Enter department name"
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
                    placeholder="e.g., DEPT001"
                    :class="{ 'border-destructive': model.errors.code }"
                />
                <p v-if="model.errors.code" class="text-xs text-destructive">
                    {{ model.errors.code }}
                </p>
            </div>
        </div>

        <!-- Description -->
        <div class="space-y-2">
            <Label for="description">Description</Label>
            <Textarea
                id="description"
                v-model="model.description"
                placeholder="Enter department description"
                rows="3"
            />
            <p v-if="model.errors.description" class="text-xs text-destructive">
                {{ model.errors.description }}
            </p>
        </div>

        <!-- Head of Department & Office Location -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="head_of_department">Head of Department</Label>
                <Input
                    id="head_of_department"
                    v-model="model.head_of_department"
                    placeholder="Enter head name"
                />
                <p v-if="model.errors.head_of_department" class="text-xs text-destructive">
                    {{ model.errors.head_of_department }}
                </p>
            </div>
            <div class="space-y-2">
                <Label for="office_location">Office Location</Label>
                <Input
                    id="office_location"
                    v-model="model.office_location"
                    placeholder="e.g., Building A, Room 101"
                />
                <p v-if="model.errors.office_location" class="text-xs text-destructive">
                    {{ model.errors.office_location }}
                </p>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    type="email"
                    v-model="model.email"
                    placeholder="department@example.com"
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

        <!-- Established Year & Stats -->
        <div class="grid gap-4 sm:grid-cols-3">
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
            <div class="space-y-2">
                <Label for="total_staff">Total Staff</Label>
                <Input
                    id="total_staff"
                    type="number"
                    v-model.number="totalStaffValue"
                    placeholder="0"
                />
                <p v-if="model.errors.total_staff" class="text-xs text-destructive">
                    {{ model.errors.total_staff }}
                </p>
            </div>
            <div class="space-y-2">
                <Label for="total_students">Total Students</Label>
                <Input
                    id="total_students"
                    type="number"
                    v-model.number="totalStudentsValue"
                    placeholder="0"
                />
                <p v-if="model.errors.total_students" class="text-xs text-destructive">
                    {{ model.errors.total_students }}
                </p>
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
</template>
