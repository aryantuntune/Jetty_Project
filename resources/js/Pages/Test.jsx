import { Head } from '@inertiajs/react';

export default function Test({ message, time }) {
    return (
        <>
            <Head title="Inertia Test" />

            <div className="container mt-5">
                <div className="row justify-content-center">
                    <div className="col-md-8">
                        <div className="card">
                            <div className="card-header bg-success text-white">
                                <h4 className="mb-0">Inertia.js Test Page</h4>
                            </div>
                            <div className="card-body">
                                <div className="alert alert-success">
                                    <strong>Status:</strong> {message}
                                </div>
                                <p><strong>Server Time:</strong> {time}</p>
                                <hr />
                                <h5>What's Working:</h5>
                                <ul>
                                    <li>React is rendering</li>
                                    <li>Inertia.js is connected</li>
                                    <li>Props are being passed from Laravel</li>
                                    <li>Layout is applied (check navbar above)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

// Don't apply the authenticated layout for this test page
Test.layout = (page) => page;
