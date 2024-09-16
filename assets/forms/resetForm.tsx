import { useForm } from '@/hooks/useForm'
import { Component, PageProps } from '@/types'
import { usePage } from '@inertiajs/react'
import { Button, PasswordInput } from '@mantine/core'
import { IconEyeCheck, IconEyeOff } from '@tabler/icons-react'
import { FormEvent, useState } from 'react'

export const ResetForm: Component = ({}) => {
  const { props } = usePage<PageProps & { csrf: string; id: string; hash: string }>()
  const { csrf, id, hash } = props
  const { inertia, mantine } = useForm({
    initialValues: {
      password: '',
      confirmPassword: '',
      id: id,
      csrf: csrf,
      hash: hash
    },
    validate: values => ({
      password: values.password.length > 5 ? null : 'Please provide a password with at least 5 characters',
      confirmPassword: values.confirmPassword === values.password ? null : "The passwords don't match"
    })
  })

  function submitForm(e: FormEvent) {
    e.preventDefault()
    inertia.submit('post', '/reset')
  }

  const [isVisible, setIsVisible] = useState(false)
  const [isConfirmVisible, setIsConfirmVisible] = useState(false)

  return (
    <form onSubmit={submitForm}>
      <PasswordInput
        required
        size="md"
        visible={isVisible}
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
      <Button type="submit" mt="md" loading={inertia.processing} disabled={inertia.processing || !mantine.isValid()}>
        Reset Password
      </Button>
    </form>
  )
}
