// frontend/src/components/Layout.jsx
import React from 'react';
import { Outlet, Link } from 'react-router-dom';
import { ModeToggle } from './mode-toggle';
import { useUserStore } from '../stores/userStore';

export function Layout() {
  const { user } = useUserStore();

  return (
    <div className="flex min-h-screen flex-col">
      <header className="sticky top-0 z-40 border-b bg-background">
        <div className="container flex h-16 items-center justify-between py-4">
          <div className="flex items-center gap-2">
            <Link to="/" className="flex items-center space-x-2">
              <span className="font-bold text-xl">Media Share</span>
            </Link>
          </div>
          <div className="flex items-center gap-4">
            {user && (
              <div className="flex items-center gap-2">
                <span className="text-sm text-muted-foreground">Logged in as:</span>
                <span className="font-medium capitalize">{user}</span>
              </div>
            )}
            <ModeToggle />
          </div>
        </div>
      </header>
      <main className="flex-1 container py-6 md:py-10">
        <Outlet />
      </main>
      <footer className="border-t py-6 md:py-0">
        <div className="container flex h-16 items-center justify-between">
          <p className="text-sm text-muted-foreground">
            Media Share App &copy; {new Date().getFullYear()}
          </p>
        </div>
      </footer>
    </div>
  );
}