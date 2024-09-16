import { Theme } from '@/theme'
import { Component, PageProps } from '@/types'
import { setCookie } from '@/utils/cookies'
import { usePage } from '@inertiajs/react'
import { MantineProvider } from '@mantine/core'
import { Notifications } from '@mantine/notifications'
import { createContext, useContext, useEffect, useState } from 'react'

type Theme = 'dark' | 'light'

interface ProviderState {
  theme: Theme
  setTheme: (theme: Theme) => void
}

const initialState: ProviderState = {
  theme: 'light',
  setTheme: () => null
}

const ProviderContext = createContext<ProviderState>(initialState)

const Provider: Component = ({ children, ...props }) => {
  const { theme: initialTheme } = usePage<PageProps>().props
  const [theme, setTheme] = useState<Theme>(initialTheme)

  useEffect(() => {
    const html = window.document.documentElement
    const mantineColorScheme = html.dataset.mantineColorScheme
    if (theme !== mantineColorScheme) {
      html.dataset.mantineColorScheme = theme
      html.classList.remove('light', 'dark')
      html.classList.add(theme)
    }
  }, [theme])

  const value = {
    theme,
    setTheme: (theme: Theme) => {
      setCookie('theme', theme)
      setTheme(theme)
    }
  }

  return (
    <ProviderContext.Provider {...props} value={value}>
      <MantineProvider theme={Theme}>
        <Notifications />
        {children}
      </MantineProvider>
    </ProviderContext.Provider>
  )
}

const useTheme = () => {
  const context = useContext(ProviderContext)
  if (context === undefined) throw new Error('useTheme must be used within the ThemeProvider')
  return context
}

export { Provider, useTheme }
