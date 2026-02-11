import { UserCog } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffForm from '@/Components/StaffForm';

export default function ManagerCreate({ branches, ferryboats }) {
    return (
        <StaffForm
            role="manager"
            Icon={UserCog}
            branches={branches}
            ferryboats={ferryboats}
            showBranch={true}
            showFerry={true}
            branchRequired={true}
            ferryRequired={true}
        />
    );
}

ManagerCreate.layout = (page) => <Layout children={page} />;
