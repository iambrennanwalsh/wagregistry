import { useForm } from '@/hooks/useForm'
import { Component } from '@/types'
import { Button, Checkbox, PasswordInput, TextInput } from '@mantine/core'
import { IconEyeCheck, IconEyeOff } from '@tabler/icons-react'
import { FormEvent, useState } from 'react'

export const SignUpForm: Component<{ oauth?: { id: string; name: string; email: string } }> = ({ oauth }) => {
  const { inertia, mantine } = useForm({
    initialValues: {
      email: oauth?.email ?? '',
      name: oauth?.name ?? '',
      password: '',
      confirmPassword: '',
      terms: false
    },
    validate: values => ({
      email: /^\S+@\S+$/.test(values.email) ? null : 'Invalid email address.',
      name: values.name.length > 0 ? null : 'Please provide your name',
      password: values.password.length > 6 ? null : 'Please provide a password at least 7 characters long',
      confirmPassword: values.confirmPassword === values.password ? null : "The passwords don't match",
      terms: values.terms === true ? null : 'You must accept our terms and conditions'
    })
  })

  function submitForm(e: FormEvent) {
    e.preventDefault()
    inertia.submit('post', '/signup')
  }

  const [isVisible, setIsVisible] = useState(false)
  const [isConfirmVisible, setIsConfirmVisible] = useState(false)

  return (
    <form onSubmit={submitForm}>
      <TextInput
        required
        size="md"
        type="email"
        disabled={oauth?.email ? true : false}
        {...mantine.getInputProps('email', { label: 'Email Address', placeholder: 'satoshi@gmail.com' })}
      />
      <TextInput
        required
        size="md"
        type="text"
        mt="md"
        disabled={oauth?.name ? true : false}
        {...mantine.getInputProps('name', { label: 'Name', placeholder: 'Satoshi Nakamoto' })}
      />

      <PasswordInput
        required
        size="md"
        visible={isVisible}
        mt="md"
        onVisibilityChange={() => setIsVisible(!isVisible)}
        visibilityToggleIcon={({ reveal }) =>
          reveal ? (
            <IconEyeOff style={{ width: 'var(--psi-icon-size)', height: 'var(--psi-icon-size)' }} />
          ) : (
            <IconEyeCheck style={{ width: 'var(--psi-icon-size)', height: 'var(--psi-icon-size)' }} />
          )
        }
        {...mantine.getInputProps('password', { label: 'Password' })}
      />

      <PasswordInput
        required
        visible={isConfirmVisible}
        size="md"
        mt="md"
        onVisibilityChange={() => setIsConfirmVisible(!isConfirmVisible)}
        visibilityToggleIcon={({ reveal }) =>
          reveal ? (
            <IconEyeOff style={{ width: 'var(--psi-icon-size)', height: 'var(--psi-icon-size)' }} />
          ) : (
            <IconEyeCheck style={{ width: 'var(--psi-icon-size)', height: 'var(--psi-icon-size)' }} />
          )
        }
        type={isConfirmVisible ? 'text' : 'password'}
        {...mantine.getInputProps('confirmPassword', { label: 'Confirm Password' })}
      />

      <Checkbox
        size="md"
        mt="md"
        {...mantine.getInputProps('terms', { type: 'checkbox', label: 'I agree to the terms' })}
        color="green"
      />

      <Button
        type="submit"
        fullWidth
        mt="md"
        loading={inertia.processing}
        disabled={inertia.processing || !mantine.isValid()}>
        Sign Up
      </Button>
    </form>
  )
}
