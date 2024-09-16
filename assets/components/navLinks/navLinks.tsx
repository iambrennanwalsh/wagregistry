import { Link } from '@inertiajs/react'
import { Anchor } from '@mantine/core'
import classes from './navLinks.module.css'

const mainLinks = [
  { href: '/', label: 'Home' },
  {
    href: '/about',
    label: 'About'
  },
  { href: '#', label: 'Blog' },
  { href: '/contact', label: 'Contact' }
]

const authLinks = [
  { href: '/login', label: 'Log In' },
  { href: '/signup', label: 'Sign Up' }
]

const userLinks = [
  { href: '/account', label: 'My Account' },
  { href: '/logout', label: 'Logout' }
]

export const navLinks = {
  main: mainLinks,
  auth: authLinks,
  user: userLinks
}

export const createNavLinks = (url: string, links: { href: string; label: string }[]) =>
  links.map(link => (
    <Anchor
      component={Link}
      key={link.label}
      href={link.href}
      className={classes.navLink}
      data-active={url === link.href || undefined}>
      {link.label}
    </Anchor>
  ))
