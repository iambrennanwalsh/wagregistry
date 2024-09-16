import { Component } from '@/types'
import { cn } from '@/utils/cn'
import { ActionIcon } from '@mantine/core'
import { IconMoon, IconSun } from '@tabler/icons-react'
import { useTheme } from '../provider'
import classes from './themeToggle.module.css'

export const ThemeToggle: Component = () => {
  const { theme, setTheme } = useTheme()

  return (
    <ActionIcon
      onClick={() => setTheme(theme === 'light' ? 'dark' : 'light')}
      variant="transparent"
      size="lg"
      color={theme == 'light' ? 'grape' : 'yellow'}
      aria-label="Toggle color scheme">
      <IconSun className={cn(classes.icon, classes.light)} stroke={1.5} />
      <IconMoon className={cn(classes.icon, classes.dark)} stroke={1.5} />
    </ActionIcon>
  )
}
