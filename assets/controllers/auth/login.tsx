import { Head } from '@/components/head'
import { PageTitle } from '@/components/pageTitle'
import { LoginForm } from '@/forms/loginForm'
import BaseLayout from '@/layout/baseLayout'
import { Controller } from '@/types'
import { Link } from '@inertiajs/react'
import { Anchor, Box, Paper } from '@mantine/core'

const Login: Controller = () => {
  return (
    <>
      <Head title="Login" description="Login to your account." />
      <PageTitle title="Log In">
        Don't have an account?{' '}
        <Anchor component={Link} href="/signup" fw={700}>
          Sign up
        </Anchor>
      </PageTitle>
      <Paper withBorder shadow="md" p={30} mt={30} radius="md">
        <LoginForm />
      </Paper>
      <Box mt="lg" ta="center">
        <Anchor component={Link} href="/forgot" fw="700" size="sm">
          Forgot your password?
        </Anchor>
      </Box>
    </>
  )
}

Login.layout = page => <BaseLayout size={420} children={page} />

export default Login
