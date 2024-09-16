import { useForm } from '@/hooks/useForm'
import { Component, PageProps } from '@/types'
import { usePage } from '@inertiajs/react'
import { Button, Checkbox, Divider, PasswordInput, Stack, TextInput } from '@mantine/core'
import {
  IconBrandAppleFilled,
  IconBrandFacebook,
  IconBrandGoogleFilled,
  IconEyeCheck,
  IconEyeOff
} from '@tabler/icons-react'
import { FormEvent, useState } from 'react'

export const LoginForm: Component = () => {
  const { props } = usePage<PageProps & { csrf: string; lastEmail: string }>()
  const { csrf, lastEmail } = props

  const { inertia, mantine } = useForm({
    initialValues: {
      email: lastEmail ?? '',
      password: '',
      remember_me: false,
      csrf_token: csrf
    },
    validate: {
      email: (value: string) => (/^\S+@\S+$/.test(value) ? null : 'Invalid email'),
      password: (value: string) => (value.length > 0 ? null : 'Please provide your password')
    }
  })

  function submitForm(e: FormEvent) {
    e.preventDefault()
    inertia.submit('post', '/login')
  }

  const [isVisible, setIsVisible] = useState(false)
  const toggleVisibility = () => setIsVisible(!isVisible)

  return (
    <form onSubmit={submitForm}>
      <TextInput required size="md" type="email" {...mantine.getInputProps('email', { label: 'Email Address' })} />
      <PasswordInput
        size="md"
        mt="md"
        required
        visible={isVisible}
        onVisibilityChange={toggleVisibility}
        visibilityToggleIcon={({ reveal }) =>
          reveal ? (
            <IconEyeOff style={{ width: 'var(--psi-icon-size)', height: 'var(--psi-icon-size)' }} />
          ) : (
            <IconEyeCheck style={{ width: 'var(--psi-icon-size)', height: 'var(--psi-icon-size)' }} />
          )
        }
        {...mantine.getInputProps('password', { label: 'Password' })}
      />
      <Checkbox
        size="md"
        mt="md"
        {...mantine.getInputProps('remember_me', { type: 'checkbox', label: 'Remember Me?' })}
        color="green"
      />
      <Button
        type="submit"
        fullWidth
        mt="md"
        loading={inertia.processing}
        disabled={inertia.processing || !mantine.isValid()}>
        Log In
      </Button>
      <Divider label="Or" labelPosition="center" my="lg" />
      <Stack mb="md">
        <Button
          component={'a'}
          href="/oauth/facebook"
          leftSection={<IconBrandFacebook />}
          style={{ backgroundColor: '#1877f2', color: '#fff' }}
          radius="md"
          fullWidth>
          Login via Facebook
        </Button>
        <Button
          component={'a'}
          href="/oauth/apple"
          leftSection={<IconBrandAppleFilled />}
          style={{ backgroundColor: '#000', color: '#fff' }}
          radius="md"
          fullWidth>
          Login via Apple
        </Button>
        <Button
          leftSection={<IconBrandGoogleFilled />}
          component={'a'}
          href="/oauth/google"
          style={{ backgroundColor: '#34A853', color: '#fff' }}
          radius="md"
          fullWidth>
          Login Via Google
        </Button>
      </Stack>
    </form>
  )
}
