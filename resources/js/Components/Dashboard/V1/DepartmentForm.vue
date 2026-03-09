<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import TiptapEditor from '@/components/TiptapEditor.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Building2 } from 'lucide-vue-next';
import type { InertiaForm } from '@inertiajs/vue3';
import type { DepartmentFormData, SchoolOption } from '@school/types';

interface Props {
    form: InertiaForm<DepartmentFormData>;
    mode?: 'create' | 'edit';
    schools: SchoolOption[];
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
    schools: () => [],
});

// Convert school_id to string for Select component
const schoolIdString = computed({
    get: () => props.form.school_id ? String(props.form.school_id) : '',
    set: (val: string) => {
        props.form.school_id = val;
    },
});

// Convert status to boolean
const isActive = computed({
    get: () => props.form.status,
    set: (val: boolean) => {
        props.form.status = val;
    },
});

// Nullable numbers workaround
const establishedYearValue = computed({
    get: () => props.form.established_year ?? undefined,
    set: (val: string | number | undefined | null) => {
        props.form.established_year = val ? Number(val) : null;
    }
});

const totalStudentsValue = computed({
    get: () => props.form.total_students ?? undefined,
    set: (val: string | number | undefined | null) => {
        props.form.total_students = val ? Number(val) : null;
    }
});

const totalStaffValue = computed({
    get: () => props.form.total_staff ?? undefined,
    set: (val: string | number | undefined | null) => {
        props.form.total_staff = val ? Number(val) : null;
    }
});

// Computed property for TiptapEditor v-model
const editorContent = computed({
    get: () => props.form.description ?? '',
    set: (val: string) => {
        props.form.description = val;
    },
});
</script>

<template>
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left Column: Main Form Fields -->
        <div class="space-y-6 lg:col-span-2">
            <!-- School Selection Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Assign to School</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-2">
                        <Label for="school_id" class="sr-only">School</Label>
                        <Select v-model="schoolIdString">
                            <SelectTrigger
                                class="h-12 w-full text-base"
                                :class="{ 'border-destructive': props.form.errors.school_id }"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-primary/10">
                                        <Building2 class="h-4 w-4 text-primary" />
                                    </div>
                                    <SelectValue placeholder="Select a school to assign this department" />
                                </div>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="school in props.schools"
                                    :key="school.value"
                                    :value="String(school.value)"
                                >
                                    <div class="flex items-center gap-2">
                                        <Building2 class="h-4 w-4 text-muted-foreground" />
                                        {{ school.label }}
                                    </div>
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="props.form.errors.school_id" class="text-xs text-destructive">
                            {{ props.form.errors.school_id }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Basic Information Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Basic Information</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Name -->
                        <div class="space-y-2 sm:col-span-2">
                            <Label for="name">Department Name <span class="text-destructive">*</span></Label>
                            <Input
                                id="name"
                                v-model="props.form.name"
                                placeholder="e.g., Computer Science"
                                :class="{ 'border-destructive': props.form.errors.name }"
                            />
                            <p v-if="props.form.errors.name" class="text-xs text-destructive">
                                {{ props.form.errors.name }}
                            </p>
                        </div>

                        <!-- Code -->
                        <div class="space-y-2">
                            <Label for="code">Code</Label>
                            <Input
                                id="code"
                                v-model="props.form.code"
                                placeholder="e.g., CS-001"
                                :class="{ 'border-destructive': props.form.errors.code }"
                            />
                            <p v-if="props.form.errors.code" class="text-xs text-destructive">
                                {{ props.form.errors.code }}
                            </p>
                        </div>

                        <!-- Head of Department -->
                        <div class="space-y-2">
                            <Label for="head_of_department">Head of Department</Label>
                            <Input
                                id="head_of_department"
                                v-model="props.form.head_of_department"
                                placeholder="e.g., Dr. John Smith"
                            />
                            <p v-if="props.form.errors.head_of_department" class="text-xs text-destructive">
                                {{ props.form.errors.head_of_department }}
                            </p>
                        </div>

                        <!-- Office Location -->
                        <div class="space-y-2">
                            <Label for="office_location">Office Location</Label>
                            <Input
                                id="office_location"
                                v-model="props.form.office_location"
                                placeholder="e.g., Building A, Room 101"
                            />
                            <p v-if="props.form.errors.office_location" class="text-xs text-destructive">
                                {{ props.form.errors.office_location }}
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <Label for="description">Description</Label>
                        <TiptapEditor
                            v-model="editorContent"
                            placeholder="Enter department description..."
                            min-height="120px"
                            max-height="250px"
                        />
                        <p v-if="props.form.errors.description" class="text-xs text-destructive">
                            {{ props.form.errors.description }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Contact Information Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Contact Information</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <!-- Email -->
                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                v-model="props.form.email"
                                placeholder="department@university.edu"
                            />
                            <p v-if="props.form.errors.email" class="text-xs text-destructive">
                                {{ props.form.errors.email }}
                            </p>
                        </div>

                        <!-- Phone -->
                        <div class="space-y-2">
                            <Label for="phone">Phone</Label>
                            <Input
                                id="phone"
                                v-model="props.form.phone"
                                placeholder="+1 234 567 8900"
                            />
                            <p v-if="props.form.errors.phone" class="text-xs text-destructive">
                                {{ props.form.errors.phone }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Right Column: Statistics & Status -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Statistics Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Statistics</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- Established Year -->
                    <div class="space-y-2">
                        <Label for="established_year">Established Year</Label>
                        <Input
                            id="established_year"
                            type="number"
                            v-model.number="establishedYearValue"
                            placeholder="e.g., 1990"
                        />
                        <p v-if="props.form.errors.established_year" class="text-xs text-destructive">
                            {{ props.form.errors.established_year }}
                        </p>
                    </div>

                    <!-- Total Students -->
                    <div class="space-y-2">
                        <Label for="total_students">Total Students</Label>
                        <Input
                            id="total_students"
                            type="number"
                            v-model.number="totalStudentsValue"
                            placeholder="0"
                        />
                        <p v-if="props.form.errors.total_students" class="text-xs text-destructive">
                            {{ props.form.errors.total_students }}
                        </p>
                    </div>

                    <!-- Total Staff -->
                    <div class="space-y-2">
                        <Label for="total_staff">Total Staff</Label>
                        <Input
                            id="total_staff"
                            type="number"
                            v-model.number="totalStaffValue"
                            placeholder="0"
                        />
                        <p v-if="props.form.errors.total_staff" class="text-xs text-destructive">
                            {{ props.form.errors.total_staff }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Status Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Status</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium">Department Status</p>
                            <p class="text-xs text-muted-foreground">
                                {{ isActive ? 'Department is active' : 'Department is inactive' }}
                            </p>
                        </div>
                        <Switch v-model="isActive" />
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
