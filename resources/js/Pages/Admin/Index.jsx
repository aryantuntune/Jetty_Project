import { Head } from '@inertiajs/react';
import { Shield } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffTable from '@/Components/StaffTable';

export default function AdminIndex({ administrators }) {
    return (
        <>
            <Head title="Administrators" />
            <StaffTable
                role="admin"
                data={administrators}
                Icon={Shield}
                showBranch={false}
                showFerry={false}
            />
        </>
    );
}

AdminIndex.layout = (page) => <Layout children={page} />;
