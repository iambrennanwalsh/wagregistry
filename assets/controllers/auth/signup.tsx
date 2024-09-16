import { Head } from '@/components/head'
import { PageTitle } from '@/components/pageTitle'
import { SignUpForm } from '@/forms/signUpForm'
import BaseLayout from '@/layout/baseLayout'
import { type Controller } from '@/types'
import { Link } from '@inertiajs/react'
import { Anchor, Paper } from '@mantine/core'

const SignUp: Controller<{ oauth: { id: string; name: string; email: string } }> = ({ oauth }) => {
  return (
    <>
      <Head title="Sign Up" description="Sign up for Chartnectar." />
      <PageTitle title="Sign Up">
        Already have an account?{' '}
        <Anchor component={Link} href="/login" fw={700}>
          Log in
        </Anchor>
      </PageTitle>
      <Paper withBorder shadow="md" p={30} mt={30} radius="md">
        <SignUpForm oauth={oauth} />
      </Paper>
    </>
  )
}

SignUp.layout = page => <BaseLayout size={420} children={page} />

export default SignUp
