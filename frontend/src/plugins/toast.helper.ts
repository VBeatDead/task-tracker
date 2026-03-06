type ToastType = 'success' | 'error' | 'warning'

interface ToastOptions {
  message: string
  type: ToastType
  duration?: number
}

const TOAST_DURATION_MS = 3000

function createToastElement(message: string, type: ToastType): HTMLDivElement {
  const colorMap: Record<ToastType, string> = {
    success: '#2F9E44',
    error: '#C92A2A',
    warning: '#E67700',
  }

  const toast = document.createElement('div')
  toast.textContent = message
  toast.style.cssText = `
    position: fixed; top: 20px; right: 20px; z-index: 9999;
    background: ${colorMap[type]}; color: white;
    padding: 12px 20px; border-radius: 6px;
    font-size: 14px; font-weight: 500;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: opacity 0.3s ease;
    max-width: 360px;
  `
  return toast
}

export function showToast({ message, type, duration = TOAST_DURATION_MS }: ToastOptions): void {
  const toast = createToastElement(message, type)
  document.body.appendChild(toast)

  setTimeout(() => {
    toast.style.opacity = '0'
    setTimeout(() => toast.remove(), 300)
  }, duration)
}

export const toast = {
  success: (message: string) => showToast({ message, type: 'success' }),
  error: (message: string) => showToast({ message, type: 'error' }),
  warning: (message: string) => showToast({ message, type: 'warning' }),
}
