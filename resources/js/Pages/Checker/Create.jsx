import { QrCode } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffForm from '@/Components/StaffForm';

export default function CheckerCreate({ branches, ferryboats }) {
    return (
        <StaffForm
            role="checker"
            Icon={QrCode}
            branches={branches}
            ferryboats={ferryboats}
            showBranch={true}
            showFerry={true}
            branchRequired={true}
            ferryRequired={true}
        />
    );
}

CheckerCreate.layout = (page) => <Layout children={page} />;
