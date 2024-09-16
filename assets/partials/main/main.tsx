import { Component } from '@/types'
import { AppShell, Container, MantineSize } from '@mantine/core'
import classes from './main.module.css'

type MainProps = {
  size?: 'fluid' | MantineSize | (string & {}) | number
}

export const Main: Component<MainProps> = ({ size, children }) => {
  return (
    <AppShell.Main className={classes.main}>
      <Container {...(size && size == 'fluid' ? { fluid: true } : { size: size })}>{children}</Container>
    </AppShell.Main>
  )
}
