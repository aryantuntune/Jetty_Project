import { Head } from '@inertiajs/react';
import { QrCode } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffTable from '@/Components/StaffTable';

export default function CheckerIndex({ checkers, isManager }) {
    return (
        <>
            <Head title="Checkers" />
            <StaffTable
                role="checker"
                data={checkers}
                Icon={QrCode}
                showBranch={true}
                showFerry={true}
            />
        </>
    );
}

CheckerIndex.layout = (page) => <Layout children={page} />;
