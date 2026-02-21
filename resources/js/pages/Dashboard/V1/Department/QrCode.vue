<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { ArrowLeft, Printer, Download, QrCode as QrCodeIcon } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import { onMounted, ref } from 'vue';
import QRCode from 'qrcode';

interface Props {
    department: {
        id: number;
        name: string;
        code: string | null;
        school_name: string | null;
    };
    qrData: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Departments', href: '/dashboard/departments' },
    { title: props.department.name, href: `/dashboard/departments/${props.department.id}` },
    { title: 'QR Code', href: `/dashboard/departments/${props.department.id}/qr-code` },
];

const qrCodeDataUrl = ref<string>('');
const qrCodeSvg = ref<string>('');

onMounted(async () => {
    try {
        // Generate QR code as data URL for display
        qrCodeDataUrl.value = await QRCode.toDataURL(props.qrData, {
            width: 300,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#ffffff',
            },
        });

        // Generate QR code as SVG for better print quality
        qrCodeSvg.value = await QRCode.toString(props.qrData, {
            type: 'svg',
            width: 300,
            margin: 2,
        });
    } catch (error) {
        console.error('Failed to generate QR code:', error);
    }
});

const handleBack = () => {
    router.visit(`/dashboard/departments/${props.department.id}`);
};

const handlePrint = () => {
    const printWindow = window.open('', '_blank');
    if (printWindow) {
        const scriptTag = '<' + 'script>window.onload = function() { window.print(); window.close(); }</' + 'script>';
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>QR Code - ${props.department.name}</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        min-height: 100vh;
                        margin: 0;
                        padding: 20px;
                        box-sizing: border-box;
                    }
                    .container {
                        text-align: center;
                        border: 2px solid #000;
                        padding: 30px;
                        border-radius: 10px;
                    }
                    .title {
                        font-size: 24px;
                        font-weight: bold;
                        margin-bottom: 10px;
                    }
                    .subtitle {
                        font-size: 16px;
                        color: #666;
                        margin-bottom: 20px;
                    }
                    .qr-code {
                        margin: 20px 0;
                    }
                    .qr-code svg {
                        width: 250px;
                        height: 250px;
                    }
                    .instruction {
                        font-size: 14px;
                        color: #333;
                        margin-top: 20px;
                        padding: 10px;
                        background: #f5f5f5;
                        border-radius: 5px;
                    }
                    .code {
                        font-size: 12px;
                        color: #999;
                        margin-top: 10px;
                    }
                    @media print {
                        body { margin: 0; padding: 0; }
                        .container { border: 2px solid #000; }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="title">${props.department.name}</div>
                    <div class="subtitle">${props.department.school_name || ''}</div>
                    <div class="qr-code">${qrCodeSvg.value}</div>
                    <div class="instruction">
                        Scan this QR code to record your attendance
                    </div>
                    <div class="code">Code: ${props.department.code || 'N/A'}</div>
                </div>
                ${scriptTag}
            </body>
            </html>
        `);
        printWindow.document.close();
    }
};

const handleDownload = () => {
    if (!qrCodeDataUrl.value) return;

    const link = document.createElement('a');
    link.download = `qr-code-${props.department.code || props.department.id}.png`;
    link.href = qrCodeDataUrl.value;
    link.click();
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`QR Code - ${department.name}`" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="ghost" size="icon" @click="handleBack">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                    <div>
                        <h1 class="text-2xl font-bold">Department QR Code</h1>
                        <p class="text-sm text-muted-foreground">
                            Print and place at department entrance for attendance tracking
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" @click="handleDownload">
                        <Download class="mr-2 h-4 w-4" />
                        Download
                    </Button>
                    <Button @click="handlePrint">
                        <Printer class="mr-2 h-4 w-4" />
                        Print
                    </Button>
                </div>
            </div>

            <!-- QR Code Card -->
            <div class="flex justify-center">
                <Card class="w-full max-w-md">
                    <CardHeader class="text-center">
                        <CardTitle class="flex items-center justify-center gap-2">
                            <QrCodeIcon class="h-5 w-5" />
                            {{ department.name }}
                        </CardTitle>
                        <CardDescription v-if="department.school_name">
                            {{ department.school_name }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col items-center space-y-4">
                        <!-- QR Code Display -->
                        <div class="rounded-lg border bg-white p-4">
                            <img
                                v-if="qrCodeDataUrl"
                                :src="qrCodeDataUrl"
                                :alt="`QR Code for ${department.name}`"
                                class="h-64 w-64"
                            />
                            <div v-else class="flex h-64 w-64 items-center justify-center">
                                <span class="text-muted-foreground">Generating QR Code...</span>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="rounded-lg bg-muted p-4 text-center">
                            <p class="text-sm font-medium">How to use:</p>
                            <ol class="mt-2 text-sm text-muted-foreground text-left list-decimal list-inside space-y-1">
                                <li>Print this QR code</li>
                                <li>Place at department entrance</li>
                                <li>Employees scan to check-in/out</li>
                                <li>Attendance is automatically recorded</li>
                            </ol>
                        </div>

                        <!-- Department Info -->
                        <div class="text-center text-sm text-muted-foreground">
                            <p v-if="department.code">Code: {{ department.code }}</p>
                            <p>ID: {{ department.id }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
