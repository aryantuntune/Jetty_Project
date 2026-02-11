import { Head } from '@inertiajs/react';
import { UserCheck } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffTable from '@/Components/StaffTable';

export default function OperatorIndex({ operators, isManager }) {
    return (
        <>
            <Head title="Operators" />
            <StaffTable
                role="operator"
                data={operators}
                Icon={UserCheck}
                showBranch={true}
                showFerry={true}
            />
        </>
    );
}

OperatorIndex.layout = (page) => <Layout children={page} />;
