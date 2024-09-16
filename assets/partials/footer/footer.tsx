import { Logo } from '@/components/logo/logo'
import { ThemeToggle } from '@/components/themeToggle/themeToggle'
import { Component } from '@/types'
import { AppShell, Container, Group } from '@mantine/core'
import classes from './footer.module.css'

export type FooterProps = {
  mainLinks: JSX.Element[]
  authLinks: JSX.Element[]
}

export const Footer: Component<FooterProps> = ({ mainLinks, authLinks }) => {
  return (
    <AppShell.Footer className={classes.shell}>
      <Container component="nav" className={classes.footer}>
        <Logo />
        <Group gap={5} visibleFrom="sm">
          {mainLinks}
        </Group>
        <Group gap={5} visibleFrom="sm">
          {authLinks}
        </Group>
        <ThemeToggle />
      </Container>
    </AppShell.Footer>
  )
}
