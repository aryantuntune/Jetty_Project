import { Shield } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffForm from '@/Components/StaffForm';

export default function AdminEdit({ admin, branches, ferryboats }) {
    return (
        <StaffForm
            role="admin"
            Icon={Shield}
            staff={admin}
            branches={branches}
            ferryboats={ferryboats}
            showBranch={true}
            showFerry={true}
            branchRequired={false}
            ferryRequired={false}
        />
    );
}

AdminEdit.layout = (page) => <Layout children={page} />;
