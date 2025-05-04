// frontend/src/components/theme-provider.jsx
import { createContext, useContext, useEffect, useState } from "react";
import { useUserStore } from "../stores/userStore";
import { logger } from "../lib/logger";
import { userPreferencesService } from "../services/UserPreferencesService";

const ThemeProviderContext = createContext({
  theme: "system",
  setTheme: () => null,
  themes: ["light", "dark", "system"],
});

export function ThemeProvider({
  children,
  defaultTheme = "system",
  storageKey = "ui-theme",
  ...props
}) {
  const [theme, setTheme] = useState(() => localStorage.getItem(storageKey) || defaultTheme);
  const { user } = useUserStore();
  
  // Load theme from local storage or user preferences
  useEffect(() => {
    const loadTheme = async () => {
      try {
        // First check local storage for theme preference
        const storedTheme = localStorage.getItem(storageKey);
        
        if (storedTheme) {
          setTheme(storedTheme);
          return;
        }
        
        // If user is logged in, try to load preferences using the service
        if (user) {
          try {
            const serverTheme = await userPreferencesService.getPreference(user, 'theme');
            if (serverTheme) {
              setTheme(serverTheme);
              // Service already handles saving fetched pref to local storage if needed, 
              // but explicit save here ensures consistency if service logic changes
              localStorage.setItem(storageKey, serverTheme); 
            } else {
              // If no theme preference on server, stick with local/default
              setTheme(localStorage.getItem(storageKey) || defaultTheme);
            }
          } catch (error) {
            // If preferences don't exist or fail to load, use local/default
            logger.warn('Failed to load theme preference via service:', error);
            setTheme(localStorage.getItem(storageKey) || defaultTheme);
          }
        } else {
           // No user, ensure we use local/default
           setTheme(localStorage.getItem(storageKey) || defaultTheme);
        }
      } catch (error) {
        logger.error('Error in theme loading:', error);
      }
    };
    
    loadTheme();
  }, [user, storageKey, defaultTheme]);

  // Apply theme to document root
  useEffect(() => {
    const root = window.document.documentElement;
    root.classList.remove("light", "dark");

    if (theme === "system") {
      const systemTheme = window.matchMedia("(prefers-color-scheme: dark)")
        .matches
        ? "dark"
        : "light";
      root.classList.add(systemTheme);
      return;
    }

    root.classList.add(theme);
  }, [theme]);
  
  // Save theme preference when it changes
  const saveTheme = async (newTheme) => {
    try {
      // Save to local storage (still useful for immediate UI update)
      localStorage.setItem(storageKey, newTheme);
      setTheme(newTheme);
      
      // If user is logged in, save using the service
      if (user) {
        try {
          await userPreferencesService.setPreference(user, 'theme', newTheme);
        } catch (error) {
          logger.warn('Failed to save theme preference to server via service:', error);
        }
      }
    } catch (error) {
      logger.error('Error saving theme preference:', error);
    }
  };

  const value = {
    theme,
    setTheme: saveTheme,
    themes: ["light", "dark", "system"],
  };

  return (
    <ThemeProviderContext.Provider {...props} value={value}>
      {children}
    </ThemeProviderContext.Provider>
  );
}

export const useTheme = () => {
  const context = useContext(ThemeProviderContext);
  if (context === undefined)
    throw new Error("useTheme must be used within a ThemeProvider");
  return context;
};