// School Module Types

export type SchoolType = 'university' | 'institute' | 'college' | 'school';

export interface School {
    id: number;
    uuid: string;
    name: string;
    code: string | null;
    type: SchoolType;
    type_label: string;
    description: string | null;
    address: string | null;
    city: string | null;
    country: string | null;
    phone: string | null;
    email: string | null;
    website: string | null;
    logo: string | null;
    established_year: number | null;
    accreditation: string | null;
    total_students: number;
    total_staff: number;
    status: boolean;
    departments_count: number | null;
    programs_count: number | null;
    employees_count: number | null;
    courses_count: number | null;
    created_at: string;
    updated_at: string;
}

export interface SchoolStats {
    total: number;
    active: number;
    inactive: number;
    universities: number;
    institutes: number;
    colleges: number;
}

export interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface PaginatedResponse<T> {
    data: T[];
    meta: PaginationMeta;
}

export interface SchoolFilters {
    status?: string;
    search?: string;
    type?: string;
}

export interface SchoolFormData {
    name: string;
    code: string;
    type: SchoolType;
    description: string;
    address: string;
    city: string;
    country: string;
    phone: string;
    email: string;
    website: string;
    logo: string;
    established_year: number | null;
    accreditation: string;
    status: boolean;
}

export interface SchoolTypeOption {
    value: SchoolType;
    label: string;
}

export interface SchoolIndexProps {
    schools: PaginatedResponse<School>;
    filters: SchoolFilters;
    stats: SchoolStats;
    types: Record<SchoolType, string>;
}

export interface SchoolShowProps {
    school: School;
    departments: any[];
    programs: any[];
    stats: {
        departments_count: number;
        programs_count: number;
        employees_count: number;
        courses_count: number;
    };
}

export interface SchoolCreateProps {
    types: Record<SchoolType, string>;
}

export interface SchoolEditProps {
    school: School;
    types: Record<SchoolType, string>;
}

export interface SchoolDeleteProps {
    school: School;
}

// Department Types

export interface SchoolOption {
    value: string;
    label: string;
}

export interface Department {
    id: number;
    uuid: string;
    school_id: number;
    name: string;
    code: string | null;
    description: string | null;
    head_of_department: string | null;
    email: string | null;
    phone: string | null;
    office_location: string | null;
    established_year: number | null;
    total_staff: number | null;
    total_students: number | null;
    status: boolean;
    school_name: string | null;
    school?: {
        id: number;
        name: string;
        code: string | null;
    };
    programs_count: number | null;
    courses_count: number | null;
    employees_count: number | null;
    created_at: string;
    updated_at: string;
}

export interface DepartmentStats {
    total: number;
    active: number;
    inactive: number;
}

export interface DepartmentFilters {
    status?: string;
    search?: string;
    school_id?: string;
}

export interface DepartmentFormData {
    school_id: string;
    name: string;
    code: string;
    description: string;
    head_of_department: string;
    email: string;
    phone: string;
    office_location: string;
    established_year: number | null;
    total_staff: number | null;
    total_students: number | null;
    status: boolean;
}

export interface DepartmentIndexProps {
    departments: PaginatedResponse<Department>;
    filters: DepartmentFilters;
    stats: DepartmentStats;
    schools: SchoolOption[];
}

export interface DepartmentShowProps {
    department: Department;
    programs: any[];
    stats: {
        programs_count: number;
        courses_count: number;
        employees_count: number;
    };
}

export interface DepartmentCreateProps {
    schools: SchoolOption[];
}

export interface DepartmentEditProps {
    department: Department;
    schools: SchoolOption[];
}

export interface DepartmentDeleteProps {
    department: Department;
}
