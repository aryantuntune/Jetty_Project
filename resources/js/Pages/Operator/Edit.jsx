import { UserCheck } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffForm from '@/Components/StaffForm';

export default function OperatorEdit({ operator, branches, ferryboats }) {
    return (
        <StaffForm
            role="operator"
            Icon={UserCheck}
            staff={operator}
            branches={branches}
            ferryboats={ferryboats}
            showBranch={true}
            showFerry={true}
            branchRequired={true}
            ferryRequired={true}
        />
    );
}

OperatorEdit.layout = (page) => <Layout children={page} />;
