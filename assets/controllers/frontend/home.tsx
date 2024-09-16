import { Head } from '@/components/head'
import { Hero } from '@/components/hero/hero'
import BaseLayout from '@/layout/baseLayout'
import { type Controller } from '@/types'
import { Button, Flex, Text, Title } from '@mantine/core'
import { IconArrowRight } from '@tabler/icons-react'
const Home: Controller = () => {
  return (
    <>
      <Head title="Home" description="Plan your trades. Journal your progress. Track your performance." />
      <Flex direction="column">
        <Title order={1} mb="md">
          Plan your trades.
          <br />
          Journal your progress.
          <br />
          Track your performance.
        </Title>
        <Text mb="sm" c="dimmed">
          WagRegistry helps you build a trading plan, journal your trades, and analyze your trading performance.
        </Text>
        <Button variant="light" rightSection={<IconArrowRight size={14} />}>
          Get Started
        </Button>
      </Flex>
    </>
  )
}

Home.layout = page => <BaseLayout hero={<Hero />} children={page} />

export default Home
