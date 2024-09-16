import { Component } from '@/types'
import { Link } from '@inertiajs/react'
import { Box, Button, Text, Title } from '@mantine/core'
import { IconArrowRight } from '@tabler/icons-react'
import classes from './hero.module.css'

export const Hero: Component = () => {
  return (
    <Box className={classes.wrapper}>
      <Box className={classes.container}>
        <Title className={classes.title}>Service Animal Registration</Title>
        <Text className={classes.description}>Wherever you go, they go too.</Text>
        <Button component={Link} href="/register" className={classes.control}>
          Easy Registration <IconArrowRight />
        </Button>
      </Box>
    </Box>
  )
}
