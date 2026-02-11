import { UserCog } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffForm from '@/Components/StaffForm';

export default function ManagerEdit({ manager, branches, ferryboats }) {
    return (
        <StaffForm
            role="manager"
            Icon={UserCog}
            staff={manager}
            branches={branches}
            ferryboats={ferryboats}
            showBranch={true}
            showFerry={true}
            branchRequired={true}
            ferryRequired={true}
        />
    );
}

ManagerEdit.layout = (page) => <Layout children={page} />;
