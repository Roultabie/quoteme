<?php
$html = <<<EOT
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
    body {
        margin-top: 5%;
        font-family: Corbel, "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", "DejaVu Sans", "Bitstream Vera Sans", "Liberation Sans", Verdana, "Verdana Ref", sans-serif;
        font-size: 0.8em;
        background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAYAAABw4pVUAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3QwcDBY6bsU1dgAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAepklEQVR42oWd2XbbSBJEi+BOyfbXz4faLZEESIrz4L41F8GERuf0aVsmgUJVLpGRC1b/+c9/nvv9vn1+frb9ft+u12tbr9ft8Xi01lobhqF9fX29/Nk//H61WrXn89m/v9ls2v1+f/n8er1uX19f7fl8tt1u16Zpmn12t9u1zWbTzufz4n33+30bx7FVP3z+cDj053k+n7Pf8TnWMI5jW61WbbVata+vr75G/p4/2+223W63/vd81t1u176+vtrj8WjP57NcX2utHY/HdrlcWmutrVarNtxut/b5+dnW63W/AYfRWpsthj+v1+vy4vz++Xy21WrVF3g8Htt6vW6r1apfh0X6MDabTRuGoU3T1M7nc9vv9/36wzC8HOp3h3E8HvvGPx6PvkYf4vP5bM/ns6+TQxuGoQ3D0LbbbT8Urp0/h8Oh7Xa7F8Gbpqnd7/f+nP7udrvtz8BhcP9hGIa+eY/Ho+12u/bdz+FwaI/HY7a5SAgH6d+11trtduuSst/v2/P5bNvttg3D0A6Hw8uG8ADjOPa/I7X83O/3vnGHw2EmNKvVavagwzB0gfj3oWeCw7r3+33XjNvt1qZpejlsNvJwOLT1et2u1+vsc2x2ZRXYD4QiNae11oavr69yU7lISuL1eu2q7hvd7/f+2e12257PZ7+Wb8ziH49H2263XYp5wHx4/1japmnq2sCa+P5+v38xjylECB4mlo3y8/N57ovgPB6P9vn5OTPr/Nxut3LfbrfbixYNw9B2u11fy2azabOnRkt8OF7gMAxts9nMNtiHww0xfdzI17B2jOPYP8PBsAlca7Va9Q1GS4ZhaKfTabZWNt3X4jto0maz6drkNeHv0kelBPug8TccuM0on/M1ea7c72ma+n7d7/c2YLc55dwQ/3x9fXXpZ2MwR17Qer1ux+OxNHdch+tP09QfwA6Q9Ww2m75gS900Tf2zrPnt7W1mejBRHMo0TTO/4h+usdvt/jrXf++PH0v/gx98PB5du7nm+XzuboDrcnjsgQ/XwGDgS6hxSor/fjwe++bY3rNZLOjxeLTL5dLGcWzb7bZfY2kzEi1tt9v+ILfbreHnMK+JuhKMYHrwEQiYn4Vrcsi3261v0vP5bJfLpW02m9nhpHPHX/BcCLcF2kLzeDz6Z3lmhKY7dR4EaOjNOp1OXRsOh0O7XC4vEswibT6snthOHmy1Ws0W7h/sPFp3PB7bdrudPdzz+WzTNPWDwaStVqsZnK38jrXeEl75LSQZ/7Tb7drz+WzH47GbbcPex+PR7vd7Xxf7gBCsVqt2PB5fTNdut5uBjCEhoCHm+Xzu0M8Iarfbtcfj0f0Hao0UV+jh+Xz2g1iCgzZ3wzC0y+XSD9PSbp/yfD7bOI7lPdMk2Uyw4dvttptf7sdBcD8gbJpY1pvIlP1ybHK/32cCzV6M4zgT7sGwsAqE0swgVWjOdrvtG5KSbziKDU8UY/PE5lwul74GYKr93DiObbPZzNAMa2FD7MBZPwjIaA9ttK2vzDXrAWEZgXEtEBOCag06nU6zZ0SbbBm+vr7+Hgi422jBC0t/wZ9vt1u/6X6/f4F21+u1nU6nmRnD8bKhPkw/QGqX45NhGDoCBD6z4T6A9HF8D+FL05YmxRG0kRbm3dDVqKmK7PEZBkEGKl3ouBgODolkkxIz2y6zuWx+hRyIuO1wE5cjMZWDt5msovY8SLTCkmsbz6HYjNk0WSh5fqD6fr9v9/u9Aw3WjdTvdru+B+krbHKt2ZhjhGuYpqn/0ofBiXORzWYzs5+r1Wpm/xI5pHQknLazw94avxs2r1arNk1T11xMZkVZWDBAeDalpkIej0dfM1pjH+lrE7lnnNZa60Hi9Xrt5pT1VYEt2lutf2Ax+/2+Lwi19uFM0zQLxuCAVqtVt4/WNEswG4i/MOLh+l9fX31zUP3WWnt/f++by8F6TYaloDJrIeYOicc0WJsNO9kwR9CpMfZBlvQ06awv/x3tShO92Wz+Bob3+/2FdGMTbf8JeDKYulwuPfp2QGRKBXa0kpyKUeVBPz4++mFa7RMdso7VajWDklwbAcBkjePYNW6/379s/jRNM6Gwj/LBmFDlz/hHNAVzZuhvpDnj59iI0+nUUYqhoO28o3XfzIjCNp6FY3dTughEHTzZnntjcbBob0XkHQ6HvpE8l9dvv4jAACw+Pz/7vVjffr9vb29v7ePjY2b7LTzJNHz3f7TVa8hnGNjA8/k8oyiwc3zBdteMKYuHT0IaDodDiZowjXzXTKvNEXkSq7O5MQJIa47Xm1QLmmptG8fxJVjEBHNQl8ulXwfHi8an+fX9uEfFRpjDM++3Xq/bUJmLpIfZbB/K7XabmQ8DgOv12i6Xy8wBJgpKJtXQFgDhwDHpFr7HA+H4/SwpfbfbrY3j+MKNZexhcOL9QROA+/ybNYHf3W632XPz5+1220GDTTBwePCm2/GxIMca3jz+803xPYZ3HBYL+fz8nDl/mygQkQO6tOtVdI/AIJXmzg6Hw8zx5yF7/c4CsmEGJBwUIAaKhOs5hAClpQmGUbcZwzIcDoe/CSp/0BS86WQjFQczPgA2spLqx+PRoavpBZu/+/3e7vd7T7suaQLAwAEZwmMCkkMx0sp8hBnmdNp20tYqUssGMGgje/Xjx4+Soc40gLVzHMe/PgTpdMRoG2iV9Ok6at1sNt9S2878VYGfP4tQGGYnqjIKSrrejtObQlRvxtgWIvM+juoBAV7P7XZrx+Ox/26apg7NOTRnNvk+vgvEZb85sBCTh9hAbGjyT5gBk3HcoDIphrouZDDJdjqdOqPKvzkNS5yBySB2sgZmVs8cHeswGqzYap6LQzATizb8/PnzhV6xWU94XgmjhQMTeLvd/uZDTOI57nh/f5/xNpWaL93Yh5JBEXl5ozk2CxLS5gpzQJxxPp+7qXT8gA9ysYNhNaYCfi0hdwaqBG/eyO+qXbbbbV+r454ELbvdbuaTbAKHrNbAAa9Wq/bnz58ZqQd342ROJrRYmKUX1OSgzBQIm+08vVEZ8DaZgCwwcACIgDm2wT+dz+eZr+TZiXuIN7Ksx4iyCmKTnzOLYbCRQeEsT2Qnyz/Y+7swILN0nD70hh/ucrnMciTWIJfe5MMu0f8cimGm+SrXU5m34vDB+b6PtcFUR9YS+N+yEAIhqxJ2z+ezB5XWFlLfM8oEthfa2Yfi8hhvlk+Vz95ut37T9/f39uPHj77AKiuXZmy/37+wsqa2vYl+CMcElrhMDBkEPB6PWZoWQrOig4DpXoMJ2DTTSH8FWgiWERzSyewth325XP6iLDtPHigpiyrN6Zu/vb218/ncfv/+PfudHy5t9Xa7bdM09X/3xrjyzzR/mo2lgrml8iELFRxe/o57G547SVYVz8EEVCkIIP/5fJ75FNCtU70bVNwPT/DGyXIofqA0LeaCcNZk1oys0s4TP3SnFpQM1zYkz4dNgXG8438/HA4zdAUMtZ/k3uyLURr7hD/ju9ZIx3DeO3JCSXJmynjgojzA5+dnV2WqR1xSwyba3xj/J6ry95IuNzwk9smIN8lDNig1BNRE2SrP5ftB5xDgJoLyMxBX8XnvgQNQ53NctMDeYSGcl3f1TmY0Nw74OOlM2donOJlDcXYijyTsfDBp75OmsbQkPQ08tLRyDfwd2pQ+z8xulufwn5+f5JsPn8O1VpnP4h4GPKbauT5CbpTX/2yaIIOql+SJuHwnjGz3k9CrSER/3gyu2YHvqkiS0QU8OOvozyBgNl9Oz/Jv5/O5XytTCN58NAbkRqxGPRfXzOr3SlBfikEyz+zsnDdumqZ2OBz6xXGGScXDcmb+3VlF23NzYbb9VVV7BqHO1RPlj+PYYxcjRCQ9zWVV4mkUeb/fZ8jM+Xn+QzscEC8VrTsXwnPNNN+4nQvB0bjAzPxMIoykClxgsF6v2/v7+wtau16vPci0Nixphnknc0fWcNaHyauqMR0LOdtY5V1ATghnojR8CHyWe1CMHisq3wc3YzKcgcvqdKgUk3VpVliknasXvd/v2/l87hwVhXAuYjB6SqIxtcS+zAEmB56aSY4kTWkVGzmfDxyldnepPwYkZUuAJrrkh33hEPnM9Xrtpv5fHzXMKi8chJl2dp5gtVq19/f3mfNHmigA4+TB3pg90EdqBRvA95zdyxRAliVVcNmkKGSpP2sSE2FzXZbBh1smHJTudruuja47qIrgjKpsLqmyx1L0nDrFZpYwNiZVHltdBUBAT1enW+oyK8nG4z/YCJw8GuA2M2cIXeHO2vEnS0UReXjWsoqbyzVjGn34mazL1oUEJK7F8nWGpCUchcLb3+/3GeWcNEaqPBKVZsIoI2nviuRzmhj1N8ytoG0Vvds8umMrHaw1fakc1r/julnHlgV97ENmJ2G9Z8KSXUtQB0lXfHx8LCISgjFfuKo7WkpKVZq2lLzy70xngFacKHNrgVlimyUqbKoKw+qwsPtZ/pR+AjIUUICPsAtwQfes6sQwFNvrAMsVJQkV3biIBLy9vc0i3GxTSP4mU5wp+fYpCX0xocBg14Fll5dzL060YapNj6clMCtewWO+h6niINxnmYUPtHsEcBlmvRXk1x3BOmDENnuzYXj5HDbWwZhNAdfJvAKw0FF69q042ZRY3xG1OSObYXzZ8/ns1Aj5mjRXmYGs8j9mkDHzruhHkDNxZYdv7mvwLwkInVP3v/k0nV83Gbnf77sGkHNf6irKwmN/Dhqiaqz0/ZNicYUkB5y1wt74qvFmGIZ2PB6pApndu2r1cz+9A8cKZWVRQxbdDb6Q88LZEoy6VR1APDQN+NYAk5e5qT9+/HiBp0hTVr9UPicPwz0gzmPc7/e+aafT6SWWyVhnu922y+XSOakqjvH13VeTOZy0BObQnNjrgmdoWaGOVF8uSMO8oaOp5iqvnEXY//zzzyzP7PLUigdLdiA308gNNJWclPmqSlupwklJNxvNejFXDoZhOirmYbvddrBENjFN4GBVysb/7yJn8tm0n3mjjXLsS3wvNguqARqFYKui56sKSm8m9VHmh5DupNkdX/Eda01CaJyy672SDc48TkWIYt7d3hfp3aH0EVUaspLU6/XaEY7tM3i8qhjMXDb5BQ4zv+PrH4/HHmWbb8M0VlUimJPUXPfVG32lM8csOwA8nU6zcqlKgPFDhADWHPYJOqkLHg9lptUbkpk3mzU3PLpi3KatyklkwObACvsPjYJTpAbMVfBQK9/FNOM49rUtle9U6V4fqMuS8AmurqwYapJSRpoIhMELbAMau3G8kQVxVYFCRdfbvGRzfaZW/cPmZjIsEU32qWc15dLUIWqomLiTvfT+O5uSLeJVD725qiXkVQ3y4Vq5L/7+8P+o7jz9zKXnwVTDV1D5nO6QxQ1QJlwjqwaNdtKEooFEx6fTqVPgROrcv6o24R5sUFWIDZ1TtXRn51gFjDI1naOv1uv1/1rackPZqCri5aGTdklMn2YvnT0qa6qca+z3+5eDIcZwYQYAwVk7YhSvBwrFaNGSCQStNNRBXlbd4Ce5L4fu+G2J/7Ng997/GQb+VwLs4E0vW2KzmKyy4x4sU2XnTK/7d8fjsY3j2K7Xa39ATAU+wxvt4jaCSsyDr5+tEz7syr84U5j3S6icZvg7Epb8x/F4bG9vb/MpFKliMJlWTSTP4X+vI4r/+/CqIrIMEFnw6XTqGB0Tg1S7QYhI2nSFCT4SRPaDSLG5Mgd9AJHK/2QKObOGSbu76NBmlJQAHBc5/M/Pz1lKYMiT9ryQtIsua8lg0mU5WapDfPHz589ZS1tSIVkxQlV8TqpzfbBNHTmSXBfckhNqtuM2myYG0zxlJ4DzN1WBnNmDdPLJenRfkhts7cA0ZTxhGiUHBpA/ydTsOI7tz58/L3FGIhCbiq+vr56By7YGz3esIKuht8uF/N1s7nGRX0XXJC3vQBpSkZpmm0l8sZlgt0z7moPzFnT92PZisxMl8cCGjo5AXTa0RFf/v8xcValoW56JqiX4m8ReorPs9PJnl4aQwYmZT8uxHaTA4QZ5ruTuZubVN/nnn39m6dGku5NGcfGbVRiJdLOkYWa1aUvwG2ola8cybeqAcqn8xmRlwvlsr8jUA/dGSGzas4Q2WyO8BtBcTtvrk/ZyfIar0nO8kJGLS0or6XIBmivUobazUM6zrzJGqFrlcK6sn/s4fso4I6l7Sz0+w11jaIH9l8e7ujbYrW3ecGaNcc2Pj4+XGIpk2e12++tDQALOCvrhbMoyNcuBcVPHHR6X5D+7mxbKxq0LdtJJZ1dmpsoyZoNP+pCkbVydQnD6+fk5q4L3oXx8fLxoTNXWnaWx3l+nfWew13YZVOKqDfo/sjklnd7SMEzGqWZTy/V67XmUpYmhaJmdok1TakKWl5r+ceu0EaP/Dz+XZbJ+Rk8mwpoQP3mgTJXrydlc1DDMqJMkBCkT9UOZxEMbso6rGnmBlHtDkweiTouNHMexm0zTNeaCHOsk8enSmkQx1cADa70Pq2K/qUpkE70uN8Ei+fYxb29vPfimz/GF2PUBLDm8TOBTiZglLCSGkjwDfVClkZrl0a0OIulQ5dquhKkSVYngqm5g1yebsnDxXTWFmmfHtNipV/kj15MhQPRsHg6HWdrXLQ1D8k0sMEe9VsVxrnb3XPWqleF6vfbD8IxGJ3t+/PgxGwKQLQj+82azeSlVSuKzGhbAdIcq/sGWm/Cz1fB8k6UqdscaJkxdeIipdrw3M1mGhEY0BDl+eKdE0yy49yIHv7jYjeQ+9piNBHZnAsvViC6co7mo+smsojfR/ox1YoZt311ojqlxsipnTFJA7viI72f1DHtiU36/3//GIVUSPstyaFnD6WY7mhnijHK/o6WTtqi+czweZ9E6Za9GYPwuqXlvPHDaiSWcLJrhaUE4XJslUBj/XoGK5LGqNyT4+WecGZmqDO1//vzZnRJUSXbpZjGCe0ZyUFkFTbN6I+kLTOb9fp+ZGUuUtdK9f/aHgBbMjgvsMiagvJN2aygOijDSB2blizsFuKff2FAFo45bNtWAeOYHuqrEmbSqtsn5CkuToV9G10T1bJqrwEmBEqz6oT8+PnoWEHPr57AJTvobqUd4soISk20S0rODPWzGReLOXFYFgBk7oQAgyv66DksnTCwBW0a6rsBI5tN20VFqmkATkOaWXJVCgMYGTNNUNvzkUGbXYdleO8FlWM13GIlRUTqVZnMQZnCXvm9T6ir32+02mxFPxD9kztf2sqKgXSrjNjV6K5AcNssHvhRI8jCJOLJ0Z4kgrFqgLfkgOxdxYOrwYTmj13O2clJQdWjm3LxmzzVOi+IO5U5KVtmvzBAuJfOdkmWCTtYvVa3SLhNaot4zRqnKUBOFVXl4C5vNUWpNVROACbU22xf4dx60n3W8mMgK4LDnfWiOoZuZxyUOKTmqqhnF9UzVPCqPJvquh4RDh8kllw8Z53F5S2U8S+iOjWUer0eQu1iv0oys9s9roxXeV1fxuGHIA6aPx+NfttdjjUAm2HTzNY4BquYYv1rCoyOcq88Kv8w7wLKmKUL7OGhsuGMhO/+ctW4/w0Gez+f+vIbCGUdZs22WoIucfXSvTGYZl4a4YRXGcfw7atxf8gSETGlW5gKJhudPROQaJsAACIPMWY5CMrKrYpOsGocOcX2tS0lTO32QPDslQ94H97wn8HCOyBuN6XYj0a9fv15mf9kyGY4P4zjONjuT+NXGsDl+0I+Pj/b+/l5m2mzSPJoCuJtw0Dbb2L2i3HmPVTUXmO8wNLnKQvpeOYPFE7KzQM9oLkeM2Ek/Ho/ZQB4shceN4/x7CjederY657hsbpqDl93YWSEkz6gFWif9nEUOhrs5CIBqk6xMX6pNzvldFsAcdGYqPpFTSnhVDGH2O8FRjkvnfvv9ft7S5rmzeRB20ORMKNfBtkMDJHXit/SwCCdoOEho8Kp9zPdn85nDm7npSjsrCTfCckUKJtV9MnlY2fpsC5Cv4xuGYUaEphDOfEgWHxgVmUJO25mNlIlychI1sDBrtpzjsP1OqfIaquIzv9GT+CcrUtzhu3Qdm6ivr69ZP8dS0GjzaKFxUTVcIEJgs8oebDab/w1SzqnQOf6oepeT62F9WK6It9oybCBfXUSLgZFOIpGsA6vMgZs3ccKgIDMQOTizKo5gw2wyf/369YKiWEOOp6rCBsxZVS/dzbRPu3ofVNao5lsEttvtDGJiyrJZknxK9UZQc1JGOtWis3fP80bSJnuCqk2S36pmnqoCIxaM379/z0ao+zOZKUyWghxQvvYpn21IlV5CV06umGq3mcMJuzYrpTypfl4VQQyTs1es/pVzBPI6NcssFSB25VOc2awyfghgNfY8NbV6jaynV1BrnOEEe+g4ZkgIaelHQxwvcNpQCuv1upeAut8QG7zf73sa1nb4eDx2W+rB9n6hjAvAXW7jzcMvOcV8uVz6W2/wB2mSyH9kStfC41fD5j7ZbANlMxuJ5lT5JmqQXwaLOpLN9uCqyt2vOjqdTi8pUNd3waxih7NOOIsoMgWQdhpzmF22rtTP1Ol3lEpOycs+Q7djGH1W4/mq1+251z81i+ZTa8t6vf7L9voGHiSc7+TISQtMmGZitEk1DzL+f+8YzHRwJdHu53NhgOMg+xZASpqrlGDu6zpfTJCHRjt3kjOA841wzqPnkDc7fh84FPyQQZIDu3xXknF7zhzMaNmFYeapqjdC+/8MZUmNsqQ7BnCloSftVNQNElwFkW69cKsCsJQqRpyzY4wMpCt/YY10m4TN8W63m7e0ZU3uEp/kzqaUwJyM7UUTOPnF9fY95sd4SE8hcgFdkpjJBCyhtJwqbRrIfSfJOjPYxi84Tu1w6jidda4/S097itzOPF/hkIvCQeYMK0uHc+lcC/PB9w0QiBscrLl68s+fPy9FbXng2d1rfsoasFSI7cmhS6/+MzFpIbOfAnBUjId7b379+jWLb5zSnpWSOuuVjtbJFCSUvEGmS5ea+626fjDMihFUVcrpkXuu7EAizTN5pGBm53y96uXHjrLzQHhuAxL82lLxHAdE7ZnJRgBBj508/cC9brCwHqeaVESqvidXUyXItY1WPEmUYoqMirPC3qywJz1Q2eFSIUyYgzD7CJsOC0a+vQDzDIvtWA3L4PFQTtxZiK0lfvWg81A942k63BJEH5wj0BxphzQZhfnkcfSmWyz1oDak0VX2zrKZ54IVoB0A5ESRXfoQ0yLO1PEmOdcbW1PcSsG7FF2Lu9TPkmYyCy0cSKcleXHqS0n89Ceu2PDkTUpLPS4Ck1L1vePIgLMEVmhaVf3oeYeehJ2TVelutU/M+ZJ+RVHGA5nvST7NLLmFqKLsK/aDZ7S29/FMMJpvb28vhchVyT99hRm8sUm2oX6NalbJuxUMTcXhJ36vaBO/O8SH7CqWSsAQIr+Y6+3trdtzt1j7+2RFbUlSW3KCkM2gh+dUfFj3IUgdLbqWtCWyr3K6HnL/Hfx0XJJ95NA0dpxVsMh9HCS6k8q0/pLW89ygK7PCVbnRx8dHL6rIRBwxW2YMyX04mnexAwKEtg2WxGpA2e12m3VH2SQtcUBI2DiOnXJ3sOfWOCeqiIwdQecEIk8hytrgZAU4fCjzCqoiOB5tiJ3Pz+Zo2irgA5p75C57Yidvs8weTNM0f+VRUsZ+QaN5HNBLcv4vI0+Vgq3eolC1xuVAADbidDotxhFOCrnLiiylC8WrPnnzbX5hZsXOfgePeR5MnQPeauBnVZM2kEuAyrbTyeFa2Y6AOcvAzGRdvrGnQkI+CJs7l/2ToczZ6b6vRzGxMbQQcN1sZavm15sjMzJMvs2vB+cZkoVw1tCoK1PN/ZVHBGUudE6nU+ULHNWaRl6tVu3Xr1+L+eclNJfvAWQzIS4z7sm8P9KaiIpD4uCAzdWkPFCizactAWumRi0no1LBk32SVeIrmQYNYxu6ihn9mEGtyL6k5H1Qv3//XkRHTugnysj3POG4ybd4QpAL3BztL9Ee+UqMPDju5UJw9sCAgWu4xaHSeO6T1Y3VkObZ+949UTMDPzezJIb2qXPaHnuRb9S0FL0MfgzHaY4sJdBEnmeeVCPHs3QJ02kqw+vP4Ziux8okUwIat3r7nlUhBf34RnL9+ybjspGyKrjOKkNrVYWMcmP8eQMKw8ScWOd26ixtrTqYMHWehk27mddof5iIqprizUa6er6K1jFnHhFCVhFYDDRPZDv45o5kgZNuOc4ki+FcVbmeNjOnBOWMkZwGTTSdb+90UEogxgG5YMD2P6s9HCNgemzyWI83i2kLS7mOygS5qM/1zd7LGWjyZrghxz85/TNz7tmpulQ5mNRCFVimj6reoMlbGzKIrdLBfmeWTW71er7UyISrpJ1tBZxqyKGYbuuAHnIw6vX1e2f+N22627+qt4plniEP0282s+SmaaMl2iAiyz4xGVmQkW848PunMgmWY0N8CJgUQ9N8g0GataWhye68Jf5xdY5pJ79Q+b9PcfD0fI5gewAAAABJRU5ErkJggg==);
    }
    div {
        width : 20%;
        margin-left: auto;
        margin-right: auto;
        padding: 2%;
        box-shadow: 1px 1px 12px #555;
        border-radius: 5px;
        background: white;
    }
    form {
        margin-bottom: 1%;
    }
    input {
        height: 5%;
        font-size: 1.2em;
        font-family: Consolas, "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", Monaco, "Courier New", Courier, monospace;
        font-weight: bold;
        color: grey;
        border: 0;
        border-radius: 3px;
        background: white;
        padding: 2px;
    }
    input[type=text], input[type=password] {
        width: 100%;
        margin-bottom: 2%;
        box-shadow: 0px 0px 3px #555 inset;
        padding-left: 5px;
    }
    input[type=text]:hover, input[type=password]:hover {
        box-shadow: 0px 0px 5px #ff944d inset;
    }
    input[type=text]:focus, input[type=password]:focus {
        box-shadow: 0px 0px 5px green inset;
    }
    input[type=submit] {
        width: 50%;
        margin-top: 3%;
        margin-left: 25%;
        box-shadow: 0px 0px 3px #555;
    }
    input[type=submit]:hover, input[type=submit]:focus {
        box-shadow: 0px 0px 5px green;
    }
    .error, .success {
        display: inline-block;
        width: 100%;
        text-align: center;
    }
    .error {
        color: red;
    }
    .success {
        color: green;
    }
    #credits {
        display: inline-block;
        width: 100%;
        text-align: center;
        margin-top: 2%;
        font-size: 0.8em;
    }
    #credits a, #credits a:visited {
        color: #555;
    }
    #credits a:hover, #credits a:focus{
        color: #222;
    }
    </style>
</head>
<body>
    <div>
        <form name="login" method="post" {$GLOBALS['loginAction']}>
            <input type="text" name="login" placeholder="Username"><br>
            <input type="password" name="pass" placeholder="Password"><br>
            <input type="submit" value="login">
        </form>
    </div>
    <span class="{$statusClass}">{$statusMessage}</span>
    <span id="credits"><a href="https://github.com/Roultabie/login" target="_blank">Login on GitHub</a></span>
</body>
</html>
EOT;
return $html;
?>