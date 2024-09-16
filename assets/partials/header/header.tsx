import { Logo } from '@/components/logo/logo'
import { ThemeToggle } from '@/components/themeToggle/themeToggle'
import { Component } from '@/types'
import { Link } from '@inertiajs/react'
import { Anchor, AppShell, Box, Burger, Container, Group, Skeleton, Text } from '@mantine/core'
import { IconMailFast } from '@tabler/icons-react'
import classes from './header.module.css'

type HeaderProps = {
  opened: boolean
  toggle: () => void
  mainLinks: JSX.Element[]
  authLinks: JSX.Element[]
  showVerificationBar: boolean
}

export const Header: Component<HeaderProps> = ({
  showVerificationBar,
  opened,
  toggle,
  mainLinks,
  authLinks,
  ...props
}) => {
  return (
    <>
      <AppShell.Header className={classes.appShellHeader}>
        {showVerificationBar && (
          <Box className={classes.verificationBar}>
            <Container size="md" className={classes.verificationBarContainer}>
              <Text className={classes.verificationText}>Don't forget to verify your email address.</Text>
              <Anchor className={classes.resendVerificationLink} component={Link} href="/verify/resend">
                <IconMailFast width="20px" /> Resend email
              </Anchor>
            </Container>
          </Box>
        )}
        <Container component="nav" size="md" className={classes.header}>
          <Logo />
          <Group gap={5} visibleFrom="sm">
            {mainLinks}
          </Group>
          <Group gap={5} visibleFrom="sm">
            {authLinks}
          </Group>
          <Group gap={10}>
            <ThemeToggle />
            <Burger opened={opened} onClick={toggle} hiddenFrom="sm" size="sm" />
          </Group>
        </Container>
      </AppShell.Header>
      <AppShell.Navbar className={classes.navbar} p="md">
        {Array(15)
          .fill(0)
          .map((_, index) => (
            <Skeleton key={index} h={28} mt="sm" animate={false} />
          ))}
      </AppShell.Navbar>
    </>
  )
}
