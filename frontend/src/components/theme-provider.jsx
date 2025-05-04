// frontend/src/components/theme-provider.jsx
import { createContext, useContext, useEffect, useState } from "react";
import { useUserStore } from "../stores/userStore";
import { logger } from "../lib/logger";
import { apiConfig } from "../lib/apiConfig";
import axios from "axios";

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
        
        // If user is logged in, try to load preferences from server
        if (user) {
          try {
            const url = apiConfig.getUrl('userPreferences', { user_name: user });
            const response = await axios.get(url);
            
            if (response.data && response.data.theme) {
              setTheme(response.data.theme);
              localStorage.setItem(storageKey, response.data.theme);
            }
          } catch (error) {
            // If preferences don't exist yet, use default
            logger.warn('Failed to load user preferences:', error);
          }
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
      // Save to local storage
      localStorage.setItem(storageKey, newTheme);
      setTheme(newTheme);
      
      // If user is logged in, save to server
      if (user) {
        try {
          const url = apiConfig.getUrl('userPreferences');
          await axios.post(url, {
            user_name: user,
            theme: newTheme
          });
        } catch (error) {
          logger.warn('Failed to save theme preference to server:', error);
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