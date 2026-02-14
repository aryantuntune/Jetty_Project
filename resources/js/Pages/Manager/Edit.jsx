import { UserCog } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffForm from '@/Components/StaffForm';

export default function ManagerEdit({ manager, branches, ferryboats, routes }) {
    return (
        <StaffForm
            role="manager"
            Icon={UserCog}
            staff={manager}
            branches={branches}
            ferryboats={ferryboats}
            routes={routes}
            showBranch={false}
            showFerry={false}
            showRoute={true}
        />
    );
}

ManagerEdit.layout = (page) => <Layout children={page} />;
