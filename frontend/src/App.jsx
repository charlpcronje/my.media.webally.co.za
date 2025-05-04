// frontend/src/App.jsx
import React, { useEffect, useState } from 'react';
import { Routes, Route, useSearchParams, Navigate } from 'react-router-dom';
import { Home } from './pages/Home';
import { MediaDetails } from './pages/MediaDetails';
import { Layout } from './components/Layout';
import { ModeToggle } from './components/mode-toggle';
import { Button } from './components/ui/button';
import { useUserStore } from './stores/userStore';
import { useToast } from './components/ui/use-toast';
import { Toaster } from './components/ui/toaster';
import { logger } from './lib/logger';

// Login form component
function LoginForm() {
  const [inputName, setInputName] = useState('');
  const [error, setError] = useState('');
  
  const handleSubmit = (e) => {
    e.preventDefault();
    const name = inputName.trim().toLowerCase();
    
    if (!name) {
      setError('Please enter your name');
      return;
    }
    
    if (name !== 'charl' && name !== 'nade') {
      setError('Only "charl" or "nade" are valid names');
      return;
    }
    
    window.location.href = `?name=${name}`;
  };
  
  return (
    <div className="flex h-screen items-center justify-center bg-background">
      <div className="w-full max-w-md p-8 space-y-6 bg-card rounded-lg shadow-lg">
        <div className="flex justify-end">
          <ModeToggle />
        </div>
        <h1 className="text-2xl font-bold text-center">Media Share</h1>
        <p className="text-center text-muted-foreground">Please enter your name:</p>
        
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="space-y-2">
            <input 
              type="text" 
              className="w-full px-3 py-2 border rounded-md border-input bg-background" 
              placeholder="Just your name"
              value={inputName}
              onChange={(e) => setInputName(e.target.value)}
            />
            {error && <p className="text-sm text-destructive">{error}</p>}
          </div>
          <Button type="submit" className="w-full py-2">
            Submit
          </Button>
        </form>
      </div>
    </div>
  );
}

export default function App() {
  const { toast } = useToast();
  const [params] = useSearchParams();
  const [loading, setLoading] = useState(true);
  const { user, setUser } = useUserStore();

  // Get user from URL param
  useEffect(() => {
    // Prevent repeated API calls if we already have a user
    if (user) {
      setLoading(false);
      return;
    }
    
    const handleUserParam = async () => {
      try {
        const nameParam = params.get('name')?.toLowerCase();
        
        if (nameParam && (nameParam === 'charl' || nameParam === 'nade')) {
          try {
            // Only make one API call attempt
            const response = await fetch(`/api/session.php?name=${nameParam}`);
            if (!response.ok) throw new Error('Session creation failed');
            
            const data = await response.json();
            if (data.success) {
              setUser(nameParam);
              toast({
                title: 'Welcome back!',
                description: `Logged in as ${nameParam}`,
              });
            }
          } catch (error) {
            // If API call fails, still set the user to avoid infinite loop
            console.error('API call failed, but setting user anyway:', error);
            setUser(nameParam);
          }
        }
        setLoading(false);
      } catch (error) {
        logger.error('Session initialization error:', error);
        toast({
          variant: 'destructive',
          title: 'Session Error',
          description: 'Failed to initialize user session',
        });
        setLoading(false);
      }
    };

    handleUserParam();
  }, [params, setUser, toast, user]);

  // If still loading, show loading indicator
  if (loading) {
    return (
      <div className="flex h-screen items-center justify-center">
        <div className="text-center">
          <div className="animate-spin h-12 w-12 border-4 border-primary border-t-transparent rounded-full mx-auto mb-4"></div>
          <p className="text-lg font-medium">Loading...</p>
        </div>
      </div>
    );
  }

  // If no valid user in URL, prompt for user
  if (!user) {
    return <LoginForm />;
  }

  return (
    <>
      <Routes>
        <Route path="/" element={<Layout />}>
          <Route index element={<Home />} />
          <Route path="/media/:id" element={<MediaDetails />} />
          <Route path="*" element={<Navigate to="/" replace />} />
        </Route>
      </Routes>
      <Toaster />
    </>
  );
}