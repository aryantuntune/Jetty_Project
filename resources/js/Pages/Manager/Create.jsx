import { UserCog } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffForm from '@/Components/StaffForm';

export default function ManagerCreate({ branches, ferryboats, routes }) {
    return (
        <StaffForm
            role="manager"
            Icon={UserCog}
            branches={branches}
            ferryboats={ferryboats}
            routes={routes}
            showBranch={false}
            showFerry={false}
            showRoute={true}
        />
    );
}

ManagerCreate.layout = (page) => <Layout children={page} />;
