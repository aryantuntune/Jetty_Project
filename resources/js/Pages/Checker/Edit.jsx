import { QrCode } from 'lucide-react';
import Layout from '@/Layouts/Layout';
import StaffForm from '@/Components/StaffForm';

export default function CheckerEdit({ checker, branches, ferryboats }) {
    return (
        <StaffForm
            role="checker"
            Icon={QrCode}
            staff={checker}
            branches={branches}
            ferryboats={ferryboats}
            showBranch={true}
            showFerry={true}
            branchRequired={true}
            ferryRequired={true}
        />
    );
}

CheckerEdit.layout = (page) => <Layout children={page} />;
