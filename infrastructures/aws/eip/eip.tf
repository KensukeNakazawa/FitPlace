resource "aws_eip" "tfer--eipalloc-0ead341c2ba118238" {
  instance             = "i-0d1bedc5ea9109633"
  network_border_group = "ap-northeast-1"
  network_interface    = "eni-0f87d8635c63659ef"
  public_ipv4_pool     = "amazon"
  vpc                  = "true"
}
