import { useForm as useInertiaForm } from '@inertiajs/react'
import { UseFormReturnType as MantineFormReturnType, UseFormInput, useForm as useMantineForm } from '@mantine/form'

interface UseFormReturnType<Values extends object> {
  inertia: ReturnType<typeof useInertiaForm<Values>>
  mantine: MantineFormReturnType<Values>
}

/** Integrates the @inertiajs/react and @mantine/form useForm hooks. */
export function useForm<Values extends object>(input: UseFormInput<Values>): UseFormReturnType<Values> {
  const inertiaForm = useInertiaForm(input.initialValues)
  const mantineForm = useMantineForm<Values>({
    validateInputOnBlur: true,
    onValuesChange: (values, previous) => {
      inertiaForm.setData({ ...values })
    },
    enhanceGetInputProps: payload => ({
      color: payload.inputProps.error ? 'danger' : 'default',
      error: inertiaForm.errors[payload.field] ?? payload.inputProps.error,
      errorProps: payload.inputProps.error && {
        className: 'form-error'
      },
      'data-testid': payload.options['id'] ?? payload.options['label'] ?? '',
      label: payload.options['label'] ?? '',
      placeholder: payload.options['placeholder'] ?? payload.options['label'] ?? ''
    }),

    ...input
  })

  return {
    inertia: inertiaForm,
    mantine: mantineForm
  }
}
