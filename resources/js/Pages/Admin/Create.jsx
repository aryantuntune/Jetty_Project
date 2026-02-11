import { Shield } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffForm from '@/Components/StaffForm';

export default function AdminCreate({ branches, ferryboats }) {
    return (
        <StaffForm
            role="admin"
            Icon={Shield}
            branches={branches}
            ferryboats={ferryboats}
            showBranch={true}
            showFerry={true}
            branchRequired={false}
            ferryRequired={false}
        />
    );
}

AdminCreate.layout = (page) => <Layout children={page} />;
