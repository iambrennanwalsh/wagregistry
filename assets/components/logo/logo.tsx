import { Link } from '@inertiajs/react'
import { Anchor, Image, Text } from '@mantine/core'
import classes from './logo.module.css'

export function Logo() {
  return (
    <Anchor className={classes.logo} component={Link} href="/">
      <Image src="/images/logo.png" h="26px" alt="WagRegistry" />
      <Text size="xl" fw={700} mt="2px" ml="xs">
        WagRegistry
      </Text>
    </Anchor>
  )
}
