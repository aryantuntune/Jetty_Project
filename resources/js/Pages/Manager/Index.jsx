import { Head } from '@inertiajs/react';
import { UserCog } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffTable from '@/Components/StaffTable';

export default function ManagerIndex({ managers }) {
    return (
        <>
            <Head title="Managers" />
            <StaffTable
                role="manager"
                data={managers}
                Icon={UserCog}
                showBranch={true}
                showFerry={true}
            />
        </>
    );
}

ManagerIndex.layout = (page) => <Layout children={page} />;
