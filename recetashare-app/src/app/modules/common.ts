import { environment } from "../environments/environment";

export function getImageUrl(imagePath: string): string {
  if (!imagePath) return '/default-image.jpg'

  if (imagePath.startsWith('http')) return imagePath

  const baseUrl = environment.apiUrl
  return `${baseUrl}/images?path=${encodeURIComponent(imagePath)}`
}
