import { Component } from 'react';

/**
 * Global Error Boundary — catches React render errors and shows a friendly
 * recovery UI instead of a blank screen. This is the ultimate failsafe for
 * any uncaught render errors (including #310).
 * 
 * Usage: wrap your app root or individual pages:
 *   <ErrorBoundary><MyPage /></ErrorBoundary>
 */
export default class ErrorBoundary extends Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false, error: null };
    }

    static getDerivedStateFromError(error) {
        return { hasError: true, error };
    }

    componentDidCatch(error, errorInfo) {
        // Log for debugging — in production this could be sent to a logging service
        console.error('[ErrorBoundary]', error, errorInfo);
    }

    handleRetry = () => {
        this.setState({ hasError: false, error: null });
    };

    handleReload = () => {
        window.location.reload();
    };

    render() {
        if (this.state.hasError) {
            return (
                <div style={{
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    minHeight: '50vh',
                    padding: '2rem',
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                }}>
                    <div style={{
                        maxWidth: 420,
                        textAlign: 'center',
                        padding: '2rem',
                        background: '#fff',
                        borderRadius: 12,
                        boxShadow: '0 4px 24px rgba(0,0,0,0.08)',
                        border: '1px solid #e2e8f0',
                    }}>
                        <div style={{ fontSize: 48, marginBottom: 12 }}>⚠️</div>
                        <h2 style={{ color: '#1e293b', margin: '0 0 8px', fontSize: 18, fontWeight: 600 }}>
                            Something went wrong
                        </h2>
                        <p style={{ color: '#64748b', fontSize: 14, margin: '0 0 20px', lineHeight: 1.5 }}>
                            An unexpected error occurred. You can try again or refresh the page.
                        </p>
                        <div style={{ display: 'flex', gap: 8, justifyContent: 'center' }}>
                            <button
                                onClick={this.handleRetry}
                                style={{
                                    padding: '8px 20px',
                                    background: '#6366f1',
                                    color: '#fff',
                                    border: 'none',
                                    borderRadius: 8,
                                    cursor: 'pointer',
                                    fontSize: 14,
                                    fontWeight: 500,
                                }}
                            >
                                Try Again
                            </button>
                            <button
                                onClick={this.handleReload}
                                style={{
                                    padding: '8px 20px',
                                    background: '#f1f5f9',
                                    color: '#475569',
                                    border: '1px solid #e2e8f0',
                                    borderRadius: 8,
                                    cursor: 'pointer',
                                    fontSize: 14,
                                    fontWeight: 500,
                                }}
                            >
                                Refresh Page
                            </button>
                        </div>
                    </div>
                </div>
            );
        }
        return this.props.children;
    }
}
