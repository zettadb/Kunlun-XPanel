<<<<<<< HEAD
import defaultSettings from '@/settings'

const title = defaultSettings.title || 'KunlunXPanel'

export default function getPageTitle(pageTitle) {
  if (pageTitle) {
    return `${pageTitle} - ${title}`
  }
  return `${title}`
}
=======
import defaultSettings from '@/settings'

const title = defaultSettings.title || 'KunlunXPanel'

export default function getPageTitle(pageTitle) {
  if (pageTitle) {
    return `${pageTitle} - ${title}`
  }
  return `${title}`
}
>>>>>>> 1.0
