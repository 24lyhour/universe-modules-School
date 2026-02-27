import { z } from 'zod';

export const departmentSchema = z.object({
    school_id: z.string().min(1, 'Please select a school'),
    name: z.string().min(1, 'Department name is required').max(255, 'Name must be less than 255 characters'),
    code: z.string().max(50, 'Code must be less than 50 characters').nullable().optional(),
    description: z.string().max(2000, 'Description must be less than 2000 characters').nullable().optional(),
    head_of_department: z.string().max(255, 'Head of department must be less than 255 characters').nullable().optional(),
    email: z.string().email('Please enter a valid email').max(255).nullable().optional().or(z.literal('')),
    phone: z.string().max(20, 'Phone must be less than 20 characters').nullable().optional(),
    office_location: z.string().max(255, 'Office location must be less than 255 characters').nullable().optional(),
    established_year: z.number().min(1800, 'Year must be at least 1800').max(new Date().getFullYear(), 'Year cannot be in the future').nullable().optional(),
    total_students: z.number().min(0, 'Total students cannot be negative').nullable().optional(),
    status: z.boolean(),
});

export type DepartmentSchemaType = z.infer<typeof departmentSchema>;
