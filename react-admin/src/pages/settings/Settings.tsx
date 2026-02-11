import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui';
import { Settings as SettingsIcon, Info, Database, Globe, Bell, Shield } from 'lucide-react';

export function Settings() {
    return (
        <div className="space-y-6">
            {/* Header */}
            <div>
                <h1 className="text-2xl font-bold text-gray-900">Settings</h1>
                <p className="text-gray-500">Application configuration and preferences</p>
            </div>

            {/* Settings Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* General Settings */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <SettingsIcon className="w-5 h-5 text-blue-600" />
                            General Settings
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="flex items-center justify-between py-2 border-b">
                            <div>
                                <p className="font-medium text-gray-900">Application Name</p>
                                <p className="text-sm text-gray-500">Jetty Ferry Management</p>
                            </div>
                        </div>
                        <div className="flex items-center justify-between py-2 border-b">
                            <div>
                                <p className="font-medium text-gray-900">Version</p>
                                <p className="text-sm text-gray-500">1.0.0</p>
                            </div>
                        </div>
                        <div className="flex items-center justify-between py-2">
                            <div>
                                <p className="font-medium text-gray-900">Environment</p>
                                <p className="text-sm text-gray-500">Development</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* API Settings */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Globe className="w-5 h-5 text-green-600" />
                            API Configuration
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="flex items-center justify-between py-2 border-b">
                            <div>
                                <p className="font-medium text-gray-900">API URL</p>
                                <p className="text-sm text-gray-500">http://localhost:8000</p>
                            </div>
                        </div>
                        <div className="flex items-center justify-between py-2 border-b">
                            <div>
                                <p className="font-medium text-gray-900">Auth Method</p>
                                <p className="text-sm text-gray-500">Laravel Sanctum</p>
                            </div>
                        </div>
                        <div className="flex items-center justify-between py-2">
                            <div>
                                <p className="font-medium text-gray-900">Status</p>
                                <span className="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                    Connected
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Database Info */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Database className="w-5 h-5 text-purple-600" />
                            Database
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="flex items-center justify-between py-2 border-b">
                            <div>
                                <p className="font-medium text-gray-900">Database Type</p>
                                <p className="text-sm text-gray-500">PostgreSQL / MySQL</p>
                            </div>
                        </div>
                        <div className="flex items-center justify-between py-2">
                            <div>
                                <p className="font-medium text-gray-900">Connection</p>
                                <span className="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                    Active
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Security */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Shield className="w-5 h-5 text-red-600" />
                            Security
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="flex items-center justify-between py-2 border-b">
                            <div>
                                <p className="font-medium text-gray-900">Session Timeout</p>
                                <p className="text-sm text-gray-500">24 hours</p>
                            </div>
                        </div>
                        <div className="flex items-center justify-between py-2">
                            <div>
                                <p className="font-medium text-gray-900">CSRF Protection</p>
                                <span className="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                    Enabled
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Info Card */}
            <Card>
                <CardContent className="p-6">
                    <div className="flex gap-4">
                        <div className="p-3 bg-blue-100 rounded-lg">
                            <Info className="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <h3 className="font-semibold text-gray-900">Need to change settings?</h3>
                            <p className="text-gray-500 mt-1">
                                Most application settings are configured through environment variables.
                                Contact your system administrator or edit the .env file on the server.
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    );
}
