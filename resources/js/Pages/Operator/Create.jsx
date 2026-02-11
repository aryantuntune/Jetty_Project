import { UserCheck } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffForm from '@/Components/StaffForm';

export default function OperatorCreate({ branches, ferryboats }) {
    return (
        <StaffForm
            role="operator"
            Icon={UserCheck}
            branches={branches}
            ferryboats={ferryboats}
            showBranch={true}
            showFerry={true}
            branchRequired={true}
            ferryRequired={true}
        />
    );
}

OperatorCreate.layout = (page) => <Layout children={page} />;
