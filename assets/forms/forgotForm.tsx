import { useForm } from '@/hooks/useForm'
import { Component } from '@/types'
import { Link } from '@inertiajs/react'
import { Anchor, Box, Button, Center, Group, TextInput, rem } from '@mantine/core'
import { IconArrowLeft } from '@tabler/icons-react'
import { FormEvent } from 'react'

export const ForgotForm: Component = () => {
  const { inertia, mantine } = useForm({
    initialValues: {
      email: ''
    },
    validate: values => ({
      email: /^\S+@\S+$/.test(values.email) ? null : 'Invalid email address.'
    })
  })

  function submitForm(e: FormEvent) {
    e.preventDefault()
    inertia.submit('post', '/forgot')
  }

  return (
    <form onSubmit={submitForm}>
      <TextInput required size="md" type="email" {...mantine.getInputProps('email', { label: 'Email Address' })} />
      <Group justify="space-between" mt="lg">
        <Anchor c="dimmed" size="sm" component={Link} href="/login">
          <Center inline>
            <IconArrowLeft style={{ width: rem(12), height: rem(12) }} stroke={1.5} />
            <Box ml={5}>Back to the login page</Box>
          </Center>
        </Anchor>
        <Button
          type="submit"
          fullWidth
          loading={inertia.processing}
          disabled={inertia.processing || !mantine.isValid()}>
          Reset Password
        </Button>
      </Group>
    </form>
  )
}
